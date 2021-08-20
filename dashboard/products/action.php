<?php require_once("../../includes/dbh.inc.php");  ?>
<?php require_once("../../includes/functions.inc.php");  ?>
<?php require_once("../../includes/session.php"); ?>
<?php 
    $page = isset($_GET['p'])?$_GET['p']:'';
    $businessId = $_SESSION["userid"];
    if($page == 'add'){
        //$fileinfo = @getimagesize($_FILES["image"]["tmp_name"]);
        $itemName = $_POST["item_name"];
        $itemIngridients = $_POST["item_ingridients"];
        $itemPrice = $_POST["item_price"];
        $itemCategorie = $_POST["item_categorie"];
        $image = $_FILES["image"]["name"];
        $target = "uploads/".basename($_FILES["image"]["name"]);

        $allowed_image_extension = array(
            "png",
            "jpg",
            "jpeg"
        );
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        if(empty($itemName) || empty($itemIngridients) || empty($itemPrice) || empty($itemCategorie) || empty($image)){
            echo "<h1 class='errorMessageCrudProducts'>Fields can't be empty! </h1>";
            //header("location: ../index.php");
        }else if (! file_exists($_FILES["image"]["tmp_name"])) {
            echo "<h1 class='errorMessageCrudProducts'>Choose image file to upload.</h1>";
            //header("location: ../index.php");
        }    // Validate file input to check if is with valid extension
        else if (! in_array($file_extension, $allowed_image_extension)) {
            echo "<h1 class='errorMessageCrudProducts'>Upload valid images. Only PNG and JPEG are allowed. </h1>";
            //header("location: ../index.php");
        }    // Validate image file size
        else if (($_FILES["image"]["size"] > 5000000)) {
            echo "<h1 class='errorMessageCrudProducts'>Image size exceeds 5MB </h1>";
            //header("location: ../index.php");
        }else{
            $sql = "INSERT INTO product (business_id, item_name, item_picture, item_ingridients, item_price, item_categorie)";
            $sql .= "VALUES (?, ?, ?, ?, ?,? )";
            if($stmt = mysqli_prepare($conn, $sql)){
                $stmt->bind_param("isssis", $business_id, $item_Name, $item_Picture, $item_Ingridients, $item_Price, $item_Categorie);
                
                $business_id = $businessId;
                $item_Name = $itemName;
                $item_Picture = $image;
                $item_Ingridients = $itemIngridients;
                $item_Price = $itemPrice;
                $item_Categorie = $itemCategorie;
            
                if($execute = mysqli_stmt_execute($stmt)) {
                    move_uploaded_file($_FILES["image"]["tmp_name"], $target);
                    echo "<h1 class='errorMessageCrudProducts'>Product uploaded successfully.</h1>";
                    //header("location: ../index.php");
                    exit();
                }else{
                    echo "<h1 class='errorMessageCrudProducts'>Problem in uploading product.</h1>";
                    //header("location: ../index.php");
                }
            }else{
                echo "<h1 class='errorMessageCrudProducts'>Error message.</h1>";
              //header("location: ../index.php");
            }
        }
    }else if($page == 'edit'){
        $itemName = $_POST["item_name"];
        $itemIngridients = $_POST["item_ingridients"];
        //$image = $_POST["image"];
        //$target = "uploads/".basename($_FILES["image"]["name"]);
        $itemPrice = $_POST["item_price"];
        $itemCategorie = $_POST["item_categorie"];
        $id = $_POST['id'];

        /*if(!empty($image)){
            $sql = "UPDATE product
                SET item_name='$itemName', item_picture = '$image', item_ingridients='$itemIngridients', item_price='$itemPrice', item_categorie='$itemCategorie'
                WHERE id = '$id'";
        }else{*/
            $sql = "UPDATE product
                    SET item_name='$itemName', item_ingridients='$itemIngridients', item_price='$itemPrice', item_categorie='$itemCategorie'
                    WHERE id = '$id'";
        /*}*/

        $Execute = mysqli_query($conn, $sql);
        //move_uploaded_file($_FILES["image"]["tmp_name"], $target);
        if($Execute){
            echo "<h1 class='errorMessageCrudProducts'>Product Updated Successfuly</h1>";
        }else{
            echo "<h1 class='errorMessageCrudProducts'>Something went wrong. Try again!</h1>";
        }
    }else if($page == 'delete'){
        $id=$_GET['id'] ;
        $sql = "DELETE FROM product WHERE id='$id'";
        if(mysqli_query($conn, $sql)){
            echo "<h1 class='errorMessageCrudProducts'>Product was deleted successfully.</h1>";
        } else{
            echo "<h1 class='errorMessageCrudProducts'>Something went wrong. Try again!</h1>";
        }
    }else{
        $limit = 5;

	    if (isset($_POST['page_no'])) {
	        $page_no = $_POST['page_no'];
	    }else{
	        $page_no = 1;
	    }
        $user_id = $_SESSION['userid'];
        $offset = ($page_no-1) * $limit;
        $sql = "SELECT * FROM product WHERE business_id = '$user_id' LIMIT $offset, $limit";
        $stmt = mysqli_query($conn, $sql) or die('error');
        if(mysqli_num_rows($stmt) > 0){ ?>
            <table>
                <tr>
                    <th>Emri i Produktit</th>
                    <th>Foto</th>
                    <th>Perberesit</th>
                    <th>Cmimi</th>
                    <th>Kategoria</th>
                    <th>Shiqimet</th>
                    <th>Data</th>
                    <th>Edito Produktin</th>
                    <th>Fshij Produktin</th>
                </tr>
                <?php 
                    while($row = mysqli_fetch_assoc($stmt)){
                        $id = $row['id'];
                        $itemName = $row['item_name'];
                        $itemPrice = $row['item_price'];
                        $itemIngredients = $row['item_ingridients'];
                        $itemCategorie = $row['item_categorie'];
                        $image = $row['item_picture'];
                        $dateAdded = $row['date_added'];
                ?>
                
                <tr id="tdata">
                    <form method="POST" id="editForm-<?php echo $row['id'] ?>"  enctype="multipart/form-data">
                        <?php if (isset($_GET['error'])) { ?>
                            <p class="error"><?php echo $_GET['error']; ?></p>
                        <?php } ?>

                        <?php if (isset($_GET['success'])) { ?>
                            <p class="success"><?php echo $_GET['success']; ?></p>
                        <?php } ?>
                        <th><input name="item_name" type="text" id="item_name-<?php echo $row['id']; ?>" name="view_product_edit_product_name" class="view_product_edit_component" value="<?php echo htmlentities($itemName) ?>" /></th>
                        <th>
                             <img width="70" height="70"  src="products/uploads/<?php echo htmlentities($image) ?>" alt="item_picture" />
                        </th>
                        <th><input type="text" name="item_ingridients" id="item_ingridients-<?php echo $row['id']; ?>"  name="view_product_edit_product_ingridients" class="view_product_edit_component" value="<?php echo htmlentities($itemIngredients) ?>" /></th>
                        <th><input style="width: 75%;" type="number" name="item_price" id="item_price-<?php echo $row['id']; ?>" step=".01" style="width: 80%;" name="view_product_edit_product_price" class="view_product_edit_component" value="<?php echo htmlentities($itemPrice) ?>" /> €</th>
                        <th>
                            <select name="item_categorie" id="item_categorie-<?php echo $row['id']; ?>" name="view_product_edit_product_categorie" class="view_product_edit_component">
                                <?php
                                    $food_categories = ["Salad", "Pizza", "Pasta", "Meat"]; 
                                    $temporary_categorie = "$itemCategorie";
                                    echo "<option value='$temporary_categorie'>$temporary_categorie</option>";
                                    foreach ($food_categories as $food_categorie) {
                                    if ($temporary_categorie != $food_categorie) {
                                        echo "<option value='$food_categorie'>$food_categorie</option>";
                                    }
                                    }
                                ?>
                            </select>
                        </th>
                        <th>2</th>
                            <?php if(strlen($dateAdded) > 11){
                                            $changeDate = date("d-m-Y", strtotime($dateAdded));
                                            $dateAdded = substr($changeDate, 0,11);
                                        } ?>
                        <th><?php echo htmlentities($dateAdded)?></th>
                        <th>
                            <button  id="submit" onclick="updateData(<?php echo $row['id']; ?>)" name="submit"  class="edit_product">Edito</button>
                        </th>
                        
                        <th>
                            <button class="delete_product" onclick="deleteData(<?php echo $row['id']; ?>)">Fshij</button>
                        </th>
                        </form> 
                </tr>
                      
                    </> <!---display_products -->
                <?php
                }  ?>
            </table><?php
                $sql3 = "SELECT * FROM product WHERE business_id = '$user_id'";

                $records = mysqli_query($conn, $sql3);

                $totalRecords = mysqli_num_rows($records);

                $totalPage = ceil($totalRecords/$limit); 
            ?>
            <!--<nav class="pagination">
                <ul class='nav-pages' style='margin:20px 0'>
                    <?php 
                    if(isset($page_no)){
                        if($page_no > 1){ 
                            
                        ?>
                            <li class="page-item">
                                <a href="" id='<?php ($page_no-1) ?>' class="page-link">&laquo;</a>
                            </li>
                        <?php }
                    }
                    for ($i=1; $i <= $totalPage ; $i++) { 
                    if ($i == $page_no) {
                        $active = "active";
                    }else{
                        $active = "";
                    }
                    ?>
                        <li class='page-item <?php echo $active?>'><a class='page-link' id='<?php echo $i ?>' href=''><?php echo $i ?></a></li>
                        <?php
                    }
                    if(isset($page_no) && !empty($page_no)){
                        if($page_no+1 <= $totalPage){ ?>
                            <li class="page-item mx-1">
                                <a href="" class="page-link" id='<?php ($page_no+1) ?>'>&raquo;</a>
                            </li>
                        <?php }
                    }
                    ?>
                </ul>
            </nav> -->
        <?php
                             
        }else{
            echo "<table></table>";
        }
    }
?>
