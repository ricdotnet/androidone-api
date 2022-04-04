<?php

  $conn = new mysqli("127.0.0.1", "ricdotnet", "12345", "comp1");

  if ($conn -> connect_errno) {
    echo "could not connect to the db";
    exit();
  }