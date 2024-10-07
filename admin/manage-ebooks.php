<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {

// Delete eBook
if(isset($_GET['del'])) {
    $id=$_GET['del'];
    $sql = "DELETE FROM tbl_ebooks WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_STR);
    $query->execute();
    echo "<script>alert('eBook deleted');</script>";
    echo "<script>window.location.href='manage-ebooks.php'</script>";
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Manage eBooks</title>
    <!-- Include your CSS files here -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
    <!-- MENU SECTION START-->
    <?php include('includes/header.php');?>
    <!-- MENU SECTION END-->

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Manage eBooks</h4>
                </div>
            </div>
            
            <!-- Table to Display eBooks -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            List of eBooks
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Book Name</th>
                                            <th>Category</th>
                                            <th>Author</th>
                                            <th>ISBN</th>
                                            <th>eBook URL</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT tbl_ebooks.id as ebookid, tbl_ebooks.eBookName, tblcategory.CategoryName, 
                                                tblauthors.AuthorName, tbl_ebooks.ISBNNumber, tbl_ebooks.eBookURL 
                                                FROM tbl_ebooks 
                                                JOIN tblcategory ON tblcategory.id = tbl_ebooks.CatId 
                                                JOIN tblauthors ON tblauthors.id = tbl_ebooks.AuthorId";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;
                                        if($query->rowCount() > 0) {
                                            foreach($results as $result) { ?>  
                                                <tr>
                                                    <td><?php echo htmlentities($cnt);?></td>
                                                    <td><?php echo htmlentities($result->eBookName);?></td>
                                                    <td><?php echo htmlentities($result->CategoryName);?></td>
                                                    <td><?php echo htmlentities($result->AuthorName);?></td>
                                                    <td><?php echo htmlentities($result->ISBNNumber);?></td>
                                                    <td><a href="<?php echo htmlentities($result->eBookURL);?>" target="_blank">View eBook</a></td>
                                                    <td>
                                                        <a href="edit-ebook.php?ebookid=<?php echo htmlentities($result->ebookid);?>"><button class="btn btn-primary">Edit</button></a>
                                                        <a href="manage-ebooks.php?del=<?php echo htmlentities($result->ebookid);?>" onclick="return confirm('Are you sure you want to delete this eBook?');"><button class="btn btn-danger">Delete</button></a>
                                                    </td>
                                                </tr>
                                                <?php $cnt++; } 
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include your JavaScript files here -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</body>
</html>
<?php } ?>
