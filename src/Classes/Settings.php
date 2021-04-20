<?php

/**
 * Settings Class that contains update and insert of key and value
 */
class Settings
{

    public function getSettings()
    {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT * FROM settings");
        $stmt->execute();
        $result = $stmt->fetchAll();

        // Create output object
        $output = (object) [
            'settings' => $result
        ];
        return $output;
    }


    public function createOrUpdateKeyValue($keyName, $keyValue)
    {

        $conn = Database::connect();
        $stmt = $conn->prepare("call update_or_insert(:keyNameVal, :keyValueVal);");
        $stmtEx = $stmt->execute([
            'keyNameVal' => $keyName,
            'keyValueVal' => $keyValue,
        ]);

        if ($stmtEx) {
            //statement was successful
            return (object) [
                'status' => 1,
                'message' => 'Api/link updated'
            ];
        } else {
            //statment faild
            return (object) [
                'status' => 0,
                'message' => 'insert faild'
            ];
        }
    }
}
