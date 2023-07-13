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
            <a class="collapse-item" href="/adminlogin/modules/'.strtolower($m['path']).'/?t=L">Zoznam</a>
            <a class="collapse-item" href="/adminlogin/modules/'.strtolower($m['path']).'/?t=A">Pridať šablónu</a>
        </div>
    </div>
</li>
';}
?>
