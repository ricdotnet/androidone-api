<?php

  require("database.php");
  global $conn;

  header("Content-Type: application/json");

  if (isset($_GET["type"]) && $_SERVER["REQUEST_METHOD"] == "GET") {
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

    if ($type == "blogs") {
      $sql    = "select * from blogs";
      $result = $conn->query($sql);

      $data = array();
      while ($row = $result->fetch_assoc()) {
        $data[] = $row;
      }

      echo json_encode($data);
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

  // Post a micro-blog
  if (isset($_GET["type"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $blogPost = json_decode(file_get_contents("php://input"));

    $user    = $blogPost->user;
    $content = $blogPost->content;

    $sql = "insert into blogs (user_id, content) values ('$user', '$content')";
    $conn->query($sql);

    echo json_encode(array("code" => 200, "message" => "Blog posted with success."));
  }