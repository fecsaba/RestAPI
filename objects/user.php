<?php
// 'user' object
class User
{
    
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;


    public $email;
    public $password;
    public $phoneident;

    public $rows = array(); // adatbázis lista
    public $success = false; // lekérdezés sikere
    public $error; // hibakód
 
    // constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }
 
    // create new user record
    function create()
    {
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    
                    email = :email,
                    password = :password,
                    phoneident= :phoneident";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->phoneident = htmlspecialchars(strip_tags($this->phoneident));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
    
        // bind the values

        $stmt->bindParam(':email', $this->email);
    
        // hash the password and phoneident before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        // $phoneident_hash = password_hash($this->phoneident, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':phoneident', $this->phoneident);
    
        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            return true;
        }
        // echo json_encode(array("stmt" => $stmt));
        return false;
    }
    
    // check if given email exist in the database
    function emailExists()
    {
    
        // query to check if email exists
        $query = "SELECT id, password, phoneident
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
    
        // bind given email value
        $stmt->bindParam(1, $this->email);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if ($num > 0) {
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = (int) $row['id'];
            $this->phoneident = $row['phoneident'];
            $this->password = $row['password'];
    
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }

    // check if given phoneident exist in the database
    function phoneIdentExists()
    {
    
        // query to check if email exists
        $query = "SELECT id, password, email
                FROM " . $this->table_name . "
                WHERE phoneident = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->phoneident = htmlspecialchars(strip_tags($this->phoneident));
        // $this->phoneident = password_hash($this->phoneident, PASSWORD_BCRYPT);
    
        // bind given email value
        $stmt->bindParam(1, $this->phoneident);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if ($num > 0) {
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->password = $row['password'];
    
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }
 
    // update a user record
    public function update()
    {
    
        // if password needs to be updated
        $password_set = !empty($this->password) ? ", password = :password" : "";
        
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    phoneident = :phoneident,
                    email = :email
                    {$password_set}
                    
                    
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        // $this->phoneident = htmlspecialchars(strip_tags($this->phoneident));
        $this->email = htmlspecialchars(strip_tags($this->email));
    
        // bind the values from the form
        // $stmt->bindParam(':phoneident', $this->phoneident);
        $stmt->bindParam(':email', $this->email);
    
        // hash the password before saving to database
        if (!empty($this->password)) {
            $this->password = htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }

        if (!empty($this->phoneident)) {
            $this->phoneident = htmlspecialchars(strip_tags($this->phoneident));
            // $phoneident_hash = password_hash($this->phoneident, PASSWORD_BCRYPT);
            $stmt->bindParam(':phoneident', $this->phoneident);
        }
    
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if ($stmt->execute()) {

            return true;
        }

        return false;
    }

    function ListUsers()
    {
    
        // query to check if email exists
        $query = "SELECT *
                FROM " . $this->table_name ;
            
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if ($num > 0) {
    
            // get record details / values
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->rows[] = $row;
            };
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }

    function DeleteUser() {
        $query = "DELETE FROM " . $this->table_name . "
                
                    
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
    
        // bind the values from the form
        $stmt->bindParam(':id', $this->id);

        // execute the query
        
        try {
            $stmt->execute();
            $this->success = true;
            

        } catch (Exception $e) {
            $this->error = $e;
            
        }

        return $this->success;
        
    }

}


?>