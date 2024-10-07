<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
    $ebookid = intval($_GET['ebookid']);

    // Fetch existing eBook data
    $sql = "SELECT * FROM tbl_ebooks WHERE id=:ebookid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':ebookid', $ebookid, PDO::PARAM_STR);
    $query->execute();
    $ebook = $query->fetch(PDO::FETCH_OBJ);

    if (!$ebook) {
        echo "<script>alert('eBook not found');</script>";
        echo "<script>window.location.href='manage-ebooks.php'</script>";
        exit();
    }

    if (isset($_POST['update'])) {
        $ebookname = $_POST['ebookname'];
        $category = $_POST['category'];
        $author = $_POST['author'];
        $ebookurl = $_POST['ebookurl'];
        $isbn = $_POST['isbn'];
        $ebookimg = $_FILES["ebookpic"]["name"];
        $imgnewname = $ebook->eBookImage;

        // If a new image is uploaded
        if ($ebookimg) {
            // Get the image extension
            $extension = strtolower(pathinfo($ebookimg, PATHINFO_EXTENSION));
            $allowed_extensions = array("jpg", "jpeg", "png", "gif");

            // Rename the image file
            $imgnewname = md5($ebookimg . time()) . '.' . $extension;

            // Check for allowed image formats
            if (!in_array($extension, $allowed_extensions)) {
                echo "<script>alert('Invalid format. Only jpg / jpeg / png / gif format allowed');</script>";
            } else {
                // Move the new image into the directory
                if (!move_uploaded_file($_FILES["ebookpic"]["tmp_name"], "ebookimg/" . $imgnewname)) {
                    echo "<script>alert('File upload failed');</script>";
                }
            }
        }

        // Update the eBook information
        $sql = "UPDATE tbl_ebooks SET eBookName=:ebookname, CatId=:category, AuthorId=:author, eBookURL=:ebookurl, ISBNNumber=:isbn, eBookImage=:imgnewname WHERE id=:ebookid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':ebookname', $ebookname, PDO::PARAM_STR);
        $query->bindParam(':category', $category, PDO::PARAM_STR);
        $query->bindParam(':author', $author, PDO::PARAM_STR);
        $query->bindParam(':ebookurl', $ebookurl, PDO::PARAM_STR);
        $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $query->bindParam(':imgnewname', $imgnewname, PDO::PARAM_STR);
        $query->bindParam(':ebookid', $ebookid, PDO::PARAM_INT);

        try {
            $query->execute();
            echo "<script>alert('eBook updated successfully');</script>";
            echo "<script>window.location.href='manage-ebooks.php'</script>";
            exit();
        } catch (PDOException $e) {
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Online Library Management System | Edit eBook</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <?php include('includes/header.php');?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Edit eBook</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">eBook Info</div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>eBook Name<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="ebookname" value="<?php echo htmlentities($ebook->eBookName); ?>" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category<span style="color:red;">*</span></label>
                                        <select class="form-control" name="category" required="required">
                                            <option value="<?php echo htmlentities($ebook->CatId); ?>"><?php echo htmlentities($ebook->CatId); ?></option>
                                            <?php
                                            $status = 1;
                                            $sql = "SELECT * from tblcategory where Status=:status";
                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':status', $status, PDO::PARAM_STR);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            foreach ($results as $result) { ?>  
                                                <option value="<?php echo htmlentities($result->id); ?>">
                                                    <?php echo htmlentities($result->CategoryName); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Author<span style="color:red;">*</span></label>
                                        <select class="form-control" name="author" required="required">
                                            <option value="<?php echo htmlentities($ebook->AuthorId); ?>"><?php echo htmlentities($ebook->AuthorId); ?></option>
                                            <?php
                                            $sql = "SELECT * from tblauthors";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            foreach ($results as $result) { ?>  
                                                <option value="<?php echo htmlentities($result->id); ?>">
                                                    <?php echo htmlentities($result->AuthorName); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>eBook URL<span style="color:red;">*</span></label>
                                        <input class="form-control" type="url" name="ebookurl" value="<?php echo htmlentities($ebook->eBookURL); ?>" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ISBN Number<span style="color:red;">*</span></label>
                                        <input class="form-control" type="text" name="isbn" value="<?php echo htmlentities($ebook->ISBNNumber); ?>" required />
                                        <p class="help-block">An ISBN is an International Standard Book Number. ISBN must be unique.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>eBook Picture</label>
                                        <input class="form-control" type="file" name="ebookpic" />
                                        <p class="help-block">Current Image: <img src="ebookimg/<?php echo htmlentities($ebook->eBookImage); ?>" width="100"></p>
                                    </div>
                                </div>
                                <button type="submit" name="update" class="btn btn-info">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
    <?php include('includes/footer.php');?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
