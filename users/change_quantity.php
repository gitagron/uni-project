<?php

  include '../includes/dbh.inc.php';
  include '../includes/session.php';

  $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
  $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);

  $real_user_id = $_SESSION['realUserid'];
  $cart_sql = "SELECT * FROM cart WHERE user_id='$real_user_id' AND status='Duke Porositur'; ";
  $cart_result = mysqli_query($conn, $cart_sql);
  $products = "";
  $quantities = "";
  if (mysqli_num_rows($cart_result) > 0) {
    $cart_id;
    $products = "";
    $quantities = "";
    while ($cart = mysqli_fetch_assoc($cart_result)) {
      $cart_id = $cart['id'];
      foreach (explode(", ", $cart['products']) as $i=>$product_id_for) {
        echo $product_id_for . " " . $product_id . " " . $quantity . " " . $i;
        if ($i == count(explode(", ", $cart['products'])) - 1) {
          $products .= $product_id_for;
          if ($product_id_for == $product_id) {
            $quantities .= $quantity;
          } else {
            $quantities .= explode(", ", $cart['quantities'])[$i];
          }
        } else {
          $products .= $product_id_for . ", ";
          if ($product_id_for == $product_id) {
            $quantities .= $quantity . ", ";
          } else {
            $quantities .= explode(", ", $cart['quantities'])[$i] . ", ";
          }
        }
      }
    }
  }

  mysqli_query($conn, "UPDATE cart SET products='$products', quantities='$quantities' WHERE id='$cart_id'; ");