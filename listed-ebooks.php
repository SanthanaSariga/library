<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
} else {
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Online Library Management System | eBook List</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Manage eBooks</h4>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">eBook List</div>
                            <div class="panel-body">
    <?php 
    $sql = "SELECT e.eBookName, c.CategoryName, a.AuthorName, e.ISBNNumber, e.eBookImage, e.eBookURL 
            FROM tbl_ebooks e 
            JOIN tblcategory c ON c.id = e.CatId 
            JOIN tblauthors a ON a.id = e.AuthorId";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $result) { 
            $imagePath = "admin/ebookimg/" . htmlentities($result->eBookImage); // Correct image path
            $defaultImage = "assets/images/default_image.jpg"; // Default image path
            
            // Check if the file exists
            if (!file_exists($imagePath)) {
                $imagePath = $defaultImage; // Use default image if not found
            }
            ?>
            <div class="col-md-4" style="float:left; height:300px;">   
                <img src="<?php echo $imagePath; ?>" width="100" alt="<?php echo htmlentities($result->eBookName); ?>">
                <br /><b><?php echo htmlentities($result->eBookName); ?></b><br />
                <?php echo htmlentities($result->CategoryName); ?><br />
                <?php echo htmlentities($result->AuthorName); ?><br />
                <?php echo htmlentities($result->ISBNNumber); ?><br />
                <a href="<?php echo htmlentities($result->eBookURL); ?>" target="_blank">Read eBook</a>
            </div>
        <?php }
    } else {
        echo "<p>No eBooks available.</p>";
    } ?>  
</div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</body>
</html>
<?php } ?>
