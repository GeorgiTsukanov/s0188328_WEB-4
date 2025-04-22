<?php

function findApplication($conn, $fio, $phone, $email) {
    try {
        $sql = "SELECT ID FROM Application WHERE 
            LastName = :lastName AND
            FirstName= :firstName AND 
            Patronymic = :patronymic AND 
            PhoneNumber = :phone AND 
            Email = :email";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':lastName', $fio["LastName"]);
        $stmt->bindParam(':firstName', $fio["FirstName"]);
        $stmt->bindParam(':patronymic', $fio["Patronymic"]);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // Вернёт ассоциативный массив или false
    } 
    catch (PDOException $e) {
        throw new Exception("Ошибка при поиске заявки: " . $e->getMessage());
    }
}
function insertApplication($conn, $fio, $phone, $email, $birthday, $gender, $biography, $languages){
    $conn->beginTransaction();
    try{
        $sql = "INSERT INTO Application (LastName, FirstName, Patronymic, PhoneNumber, Email, BirthDay, Gender, Biography) 
                VALUES (:lastName, :firstName, :patronymic, :phone, :email, :birthDay, :gender, :biography)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':lastName', $fio["LastName"]);
        $stmt->bindParam(':firstName', $fio["FirstName"]);
        $stmt->bindParam(':patronymic', $fio["Patronymic"]);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':birthDay', $birthday);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':biography', $biography);

        $stmt->execute();

        $applicationId = $conn->lastInsertId();

        $sql = "INSERT INTO FavoriteProgrammingLanguage (ID, ID_ProgrammingLanguage) VALUES (:id, :pl)";
        $stmt = $conn->prepare($sql);
    
        foreach ($languages as $language) {
            $stmt->bindParam(':id', $applicationId);
            $stmt->bindParam(':pl', $language, PDO::PARAM_INT);
            $stmt->execute();
        }
        $conn->commit();
        echo "Заявка успешно добавлена";
    }
    catch (PDOException $e) {
        $conn->rollBack();
        throw new Exception("Ошибка при добавлении заявки: " . $e->getMessage());
    }
    finally {
        $conn = null;
    }
}
function updateApplication($applicationId, $conn, $birthday, $gender, $biography, $languages){
    $conn->beginTransaction();
    try{
        $sql = "UPDATE Application 
                SET BirthDay = :birthDay, Gender = :gender, Biography = :biography 
                WHERE ID = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $applicationId);
        $stmt->bindParam(':birthDay', $birthday);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':biography', $biography);

        $stmt->execute();

        $sql = "DELETE FROM FavoriteProgrammingLanguage WHERE ID = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $applicationId);
        $stmt->execute();

        $sql = "INSERT INTO FavoriteProgrammingLanguage (ID, ID_ProgrammingLanguage) VALUES (:id, :pl)";
        $stmt = $conn->prepare($sql);
        foreach ($languages as $language) {
            $stmt->bindParam(':id', $applicationId);
            $stmt->bindParam(':pl', $language);
            $stmt->execute();
        }
        $conn->commit();
        echo "Заявка успешно обновлена!";
    }
    catch (PDOException $e) {
        $conn->rollBack();
        throw new Exception("Ошибка при обновлении заявки: " . $e->getMessage());
    }
    finally {
        $conn = null;
    }
}
function pushApplication($config, $fio, $phone, $email, $birthday, $gender, $biography, $languages){
    try {
        $conn = new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
            $config['username'],
            $config['password']
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        throw new Exception("Ошибка подключения к базе данных: " . $e->getMessage());
    }
    
    $applicationId = findApplication($conn, $fio, $phone, $email);
    if ($applicationId == False){
        //добавляем
        insertApplication($conn, $fio, $phone, $email, $birthday, $gender, $biography, $languages);
    }
    else{
        //обновляем
        updateApplication($applicationId['ID'], $conn, $birthday, $gender, $biography, $languages);
    }
}

?>