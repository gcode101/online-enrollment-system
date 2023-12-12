<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class DatabaseHandler {
	
	private $host;
    private $username;
    private $password;
    private $database;
	
    public function __construct($host, $username, $password, $database)
	{
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
	}

	public function connect()
    {
        $con = mysqli_connect($this->host, $this->username, $this->password, $this->database);
        if ($con){
        	return $con;
        } else {
        	die("Error connecting to database: " . mysqli_connect_error());	
        }
    }

    public function executeSelectQuery($con, $sql, $params = null) {
    
        // Check if parameters are provided
        if ($params !== null) {
            $stmt = $con->prepare($sql);

            // Check if prepare was successful
            if ($stmt) {
                // Bind parameters
                $stmt->bind_param(...$params);

                // Execute the statement
                $stmt->execute();

                // Get result
                $result = $stmt->get_result();

                // Fetch data
                $data = $result->fetch_all(MYSQLI_ASSOC);

                // Close the statement
                $stmt->close();

                return $data;
            }
        } else {
            $result = mysqli_query($con, $sql);

            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                mysqli_free_result($result);
                return $data;
            } else {
                return false;
            }

        }
    }

    public function executeQuery($con, $sql) {

        $result = mysqli_query($con, $sql);

        if ($result) {
            return true;
        } else {
        	echo "Error executing query: " . mysqli_error($con);
            return false;
        }
    }

	public function close($con)
    {
        mysqli_close($con);
    }
}

?>