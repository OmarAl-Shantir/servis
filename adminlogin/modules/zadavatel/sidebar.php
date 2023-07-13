<?php
if ($_SESSION['admin_role'] != 2){
  echo '
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse'.$m['name'].'"
        aria-expanded="true" aria-controls="collapse'.$m['name'].'">
        <i class="fas fa-fw '.$m['icon'].'"></i>
        <span>'.$m['name'].'</span>
    </a>
    <div id="collapse'.$m['name'].'" class="collapse" aria-labelledby="headingAccounts"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Predajca</h6>
            <a class="collapse-item" href="/adminlogin/modules/'.strtolower($m['path']).'/?t=O">Prehľad</a>
        </div>
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Zákazník</h6>
            <a class="collapse-item" href="/adminlogin/modules/'.strtolower($m['path']).'/?t=Z">Prehľad</a>
        </div>
    </div>
</li>
';
}
?>
