<?php
if ($_SESSION['admin_logged']==0){
  header("location: login.php");
}
?>
<!--<div class="modal fade show" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-modal="true" style="padding-right: 15px; display: block;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <a class="btn btn-primary" href="login.php?logout=1">Logout</a>
      </div>
    </div>
  </div>
</div>-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Naozaj sa chcete odhlásiť?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Pre potvrdenie odhlásenia klikite dole na tlačidlo "Odhlásiť".</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Zrušiť</button>
                <a class="btn btn-primary" href="/adminlogin/login.php?logout=1">Odhlásiť</a>
            </div>
        </div>
    </div>
</div>
<script src="/adminlogin/theme/vendor/jquery/jquery.min.js"></script>
<script src="/adminlogin/theme/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="/adminlogin/theme/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="/adminlogin/theme/js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->

</body>
</html>
