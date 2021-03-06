<?php
session_start();
include "checklogin.php";

header("Content-Type:text/html;");
$connection = createConnString();
function displayBookList($connection) {
    $db = new BookGateway($connection);
    // if the value is 'all' then the user specifically selected to remove that filter
    // set to null so it is treated appropriately later on
        if (isset($_GET['subcat'])){
            if (($_GET['subcat']) == "all") { $_GET['subcat'] = null; }
        }else if(isset($_GET['imprint'])) {
            if (($_GET['imprint']) == "all") { $_GET['imprint'] = null; }
        }
        
        $returnVar="";
        if(isset($_GET['subcat'])){ //if there is a subcategory query string
            $result = $db->getBooksBySubcategory($_GET['subcat']);
        }else if(isset($_GET['imprint'])){ //if there is an imprint query string
            $result = $db->getBooksByImprint($_GET['imprint']);
        }else{ //if there isn't a query string
            $result = $db->findAllLimit(null,20,null);
        }
        if ($result != false) { // check for errors getting data from mysql
            foreach($result as $row) { // go through mysql results, echo appropriate information
                $ISBN10=$row['ISBN10']; // variable for temporary storage of ISBN10
                
                // fetch sub category information
                $subCategoryReturn=$db->getSubcategoryByID($row['SubcategoryID']);
                
                // fetch imprint information
                $imprintReturn=$db->getImprintByID($row['ImprintID']);
                
                $returnVar .= "<tr><td><a href='single-book.php?ISBN10=" . $row['ISBN10'] . "'><img src='book-images/tinysquare/" . $ISBN10 . ".jpg' alt='$ISBN10'></a></td><td><a href='single-book.php?ISBN10=" . $row['ISBN10'] . "'>" . $row['Title'] . "</a></td><td>" . $row['CopyrightYear'] . "</td><td>".$subCategoryReturn['SubcategoryName']."</td><td>".$imprintReturn['Imprint']."</td></tr>";
            }
        }
        return $returnVar;
}

//this function returns the list of available subcategories
function displaySubCatList($connection) {
    $db = new BookGateway($connection);
    $returnVar="";
    $result = $db->getAllSubcategories();
    
    $returnVar .= ("<li><a href='browse-books.php?subcat=all'>All Sub Categories</a></li>");
    
    if ($result != false) { // check for errors getting data from mysql
        foreach($result as $row) {  // go through mysql results, echo appropriate information
            $returnVar .= ("<li><a href='browse-books.php?subcat=" . $row['SubCategoryID'] ."'>" . $row['SubcategoryName'] . "</a></li>");
        }
    }
    return $returnVar;
}

//this function returns the list of available imprints
function displayImprintList($connection) {
    $returnVar="";
    $db = new BookGateway($connection);

    $result= $db->getAllImprints();
    
    $returnVar .= ("<li><a href='browse-books.php?imprint=all'>All Imprints</a></li>");
    
    if ($result != false) { // check for errors getting data from mysql
        foreach($result as $row){  // go through mysql results, echo appropriate information
            $returnVar .= ("<li><a href='browse-books.php?imprint=" . $row['ImprintID'] . "'>" . $row['Imprint'] . "</a></li>");
        }
    }
    return $returnVar;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Chapter 14</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="css/material.blue-light_blue.min.css" />

    <link rel="stylesheet" href="css/styles.css">
    
    <script src="https://code.jquery.com/jquery-1.7.2.min.js" ></script>
    <script src="https://code.getmdl.io/1.1.3/material.min.js"></script>
   <script src="js/functions.js"></script>
    
</head>

<body>
    
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer
            mdl-layout--fixed-header">
            
    <?php include 'includes/header.inc.php'; ?>
    <?php include 'includes/left-nav.inc.php'; ?>
    
    <main class="mdl-layout__content mdl-color--grey-50">
        <section class="page-content">
            <div class="mdl-grid">

                <!-- mdl-cell + mdl-card-->
                <div class="mdl-cell mdl-cell--9-col card-lesson mdl-card  mdl-shadow--2dp">
                    <div class="mdl-card__title mdl-color--pink">
                        <h2 class="mdl-card__title-text">Instructions</h2>
                    </div>
                    
                    <div class="mdl-card__supporting-text">
                        <p>Below is a list of Books. You can apply filters by clicking on the links in the "Subcategory List" and "Imprint List" cards (left hand side).</p>
                    </div>
                </div> <!--  / mdl-cell + mdl-card -->
            </div>
            <div class="mdl-grid">
                <!-- mdl-cell + mdl-card -->
                <div class="mdl-cell mdl-cell--3-col card-lesson mdl-card  mdl-shadow--2dp">
                    <div class="mdl-card__title mdl-color--orange">
                        <h2 class="mdl-card__title-text">Subcategory List</h2>
                    </div>
                    
                    <div class="mdl-card__supporting-text">
                        <ul class="demo-list-item mdl-list">
                            <!-- display list of subcategories -->
                            <?php echo displaySubCatList($connection); ?>
                        </ul>
                    </div>
                </div>  <!-- / mdl-cell + mdl-card -->
                
                
              
                <!-- mdl-cell + mdl-card -->
                <div class="mdl-cell mdl-cell--9-col card-lesson mdl-card  mdl-shadow--2dp">
                    <div class="mdl-card__title mdl-color--deep-purple mdl-color-text--white">
                        <h2 class="mdl-card__title-text">Books</h2>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
                        
                            <div class="mdl-tabs__panel is-active" id="address-panel">
                                <!-- display requested employees information based on employee id -->
                                <table class="mdl-data-table  mdl-shadow--2dp">
                                    <thead>
                                        <tr><td>Cover</td><td>Title</td><td>Year</td><td>Subcategory</td><td>Imprint</td></tr>
                                    </thead>
                                    <tbody>
                                        <?php echo displayBookList($connection); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>                         
                    </div>    
              </div>  <!-- / mdl-cell + mdl-card -->   
              
              <!-- mdl-cell + mdl-card -->
                <div class="mdl-cell mdl-cell--3-col card-lesson mdl-card  mdl-shadow--2dp">
                    <div class="mdl-card__title mdl-color--green">
                        <h2 class="mdl-card__title-text">Imprint List</h2>
                    </div>
                    
                    <div class="mdl-card__supporting-text">
                        <ul class="demo-list-item mdl-list">
                            <!-- display list of imprints -->
                            <?php echo displayImprintList($connection); ?>
                        </ul>
                    </div>
                </div>  <!-- / mdl-cell + mdl-card -->
            </div>  <!-- / mdl-grid -->    
        </section>
    </main>    
</div>    <!-- / mdl-layout -->
</body>
</html>