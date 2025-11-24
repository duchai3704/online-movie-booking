<nav class="col-md-2 d-none d-md-block bg-light sidebar">
      <div class="sidebar-sticky">
        <ul class="nav flex-column">

          <?php 


            $uri = $_SERVER['REQUEST_URI']; 
            $uriAr = explode("/", $uri);
            $page = end($uriAr);

          ?>


          <li class="nav-item">
            <a class="nav-link" href="index.php">
              <span data-feather="home"></span>
              Dashboard <span class="sr-only">(current)</span>
            </a>
          </li>
         <li class="nav-item">
            <a class="nav-link" href="add-movie.php">
              <span data-feather="users"></span>
              Add Movie
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="food_orders.php">
              <span data-feather="users"></span>
              Quản lý Combos
            </a>
          </li>
          
         <li class="nav-item">
            <a class="nav-link" href="Theater_and_show.php">
              <span data-feather="users"></span>
              Theater And Show
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="customers.php">
              <span data-feather="users"></span>
              Customers
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="Feedback.php">
              <span data-feather="users"></span>
              Feedback
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="users.php">
              <span data-feather="users"></span>
              Users
            </a>
          </li>
           
        </ul>


       
      </div>
    </nav>
    <?php
include_once 'Database.php';
$result = mysqli_query($conn,"SELECT * FROM admin");

if (mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_array($result)) {
    ?>
     
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Hello <?php echo $row['name']; ?></h1>
        
      </div>
  <?php
  }
}
?>

   