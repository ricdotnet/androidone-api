<?php

  require("database.php");
  global $conn;

  header("Content-Type: application/json");

  if (isset($_GET["type"]) && $_SERVER["REQUEST_METHOD"] == "GET") {
    $type = $_GET["type"];

    $username = $_GET["username"];
    $password = $_GET["password"];
    $email    = null;

    if (isset($_GET["email"])) {
      $email = $_GET["email"];
    }

    if ($type == "login") {
      try {
        $sql    = "select * from user where username = '$username' and password = '$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          echo json_encode(array("username" => $username, "message" => "Successfully logged in."));
        } else {
          http_response_code(401);
          echo json_encode(array("error" => 401, "message" => "Invalid login details."));
        }
      } catch (Exception $error) {
        echo $error;
        return;
      }
    }


    if ($type == "register") {
      try {
        if (valueExists("username", $username, $conn)) {
          http_response_code(401);
          echo json_encode(array("error" => 401, "username" => $username, "message" => "Username already exists."));
          return;
        }

        if (valueExists("email", $email, $conn)) {
          http_response_code(401);
          echo json_encode(array("error" => 401, "email" => $email, "message" => "Email already exists."));
          return;
        }
      } catch (Exception $error) {
        echo $error;
        return;
      }

      if (createUser($username, $password, $email, $conn)) {
        echo json_encode(array("message" => "User account registered with success."));
        return;
      }

      return;
    }
  }

  function valueExists(string $column, string $value, mixed $conn): int
  {
    $sql    = "select * from user where $column = '$value'";
    $result = $conn->query($sql);

    return $result->num_rows > 0;
  }

  function createUser(string $username, string $password, string $email, mysqli $conn): bool
  {
    $sql = "insert into user (username, password, email) values ('$username', '$password', '$email')";
    $conn->query($sql);

    if ($conn->error) {
      echo "something went wrong";
      return false;
    }

    return true;
  }

  // Micro-blogging related api below
  if (isset($_GET["type"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $blogPost = json_decode(file_get_contents("php://input"));

    echo $blogPost->blog;
  }