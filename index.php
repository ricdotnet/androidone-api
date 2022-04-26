<?php

  require("database.php");
  global $conn;

  header("Content-Type: application/json");

  if (($_GET["type"] == "register" || $_GET["type"] == "login") && $_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_GET["type"];

    if (isset($_GET["username"]) && isset($_GET["password"])) {
      $username = $_GET["username"];
      $password = $_GET["password"];
      $email    = null;
    }

    if (isset($_GET["email"])) {
      $email = $_GET["email"];
    }

    if ($type == "login") {
      try {
        $sql    = "select * from users where username = '$username' and password = '$password'";
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
        echo json_encode(array("code" => 200, "message" => "User account registered with success."));
        return;
      }

      return;
    }
  }

  function valueExists(string $column, string $value, mixed $conn): int
  {
    $sql    = "select * from users where $column = '$value'";
    $result = $conn->query($sql);

    return $result->num_rows > 0;
  }

  function createUser(string $username, string $password, string $email, mysqli $conn): bool
  {
    $sql = "insert into users (username, password, email) values ('$username', '$password', '$email')";
    $conn->query($sql);

    if ($conn->error) {
      echo json_encode(array("code" => 400, "message" => "Something went wrong."));
      return false;
    }

    return true;
  }

  // Post an echo
  if ($_GET["type"] == "echo" && $_GET["action"] == "post" && $_SERVER["REQUEST_METHOD"] == "POST") {
    $echoPost = json_decode(file_get_contents("php://input"));

    $username    = $echoPost->username;
    $content = $echoPost->content;

    $sql = "insert into echoes (username, content) values ('$username', '$content')";
    $conn->query($sql);

    echo json_encode(array("code" => 200, "message" => "Echo posted with success."));
  }

  if ($_GET["type"] == "echo" && $_GET["action"] = "get" && $_SERVER["REQUEST_METHOD"] == "POST") {
    $sql    = "select * from echoes order by id desc";
    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
      $data[] = $row;
    }

    echo json_encode($data);
  }