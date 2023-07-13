<?php include "theme/page.php";

//$user = new UserView();
//$user_data = $admin->getUser();
//setcookie("model", "HLR32TS554SMART", time() + (10 * 365 * 24 * 60 * 60));
//setcookie("hash", "8c4017534950761fbb7c3ca99a679faa2b40c91726cd622f0b8168b4b9794641ec0d122644a46dac4985b6c2857cdc0ab23b0facd9cc82299b333d10b30a3ad7", time() + (10 * 365 * 24 * 60 * 60));
//require __DIR__ . '/load.php';

//$admin = new AdminView();
//$admin->logIn('admin@animus.sk','heslo');
//$options = [
//    'cost' => 12,
//];
//echo password_hash("heslo", PASSWORD_BCRYPT, $options);

var_dump($_POST);

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  </head>
  <body>
    <?php if (($_COOKIE['model'] == "HLR32TS554SMART") and ($_COOKIE['hash'] == "8c4017534950761fbb7c3ca99a679faa2b40c91726cd622f0b8168b4b9794641ec0d122644a46dac4985b6c2857cdc0ab23b0facd9cc82299b333d10b30a3ad7")){?>
      <script type="text/javascript">
        $(document).ready(function(){
          $("#display").click(function(){
            $.ajax({    //create an ajax request to display.php
              type: "POST",
              url: "service_api.php",
              data: <?php echo json_encode(array("model"=>$_COOKIE['model'], "hash"=>$_COOKIE['hash']));?>,
              dataType: "json",
              success: function(data){
                console.log(data[0]);
              }
            });
          });
        });
      </script>
        <table border="1" align="center">
           <tr>
               <td> <input type="button" id="display" value="Display All Data" /> </td>
           </tr>
        </table>
        <div id="responsecontainer" align="center">
        </div>
    <?php } ?>
  </body>
</html>
