<?php
echo '
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse'.$m['name'].'"
        aria-expanded="true" aria-controls="collapse'.$m['name'].'">
        <i class="fas fa-fw '.$m['icon'].'"></i>
        <span>'.$m['name'].'</span>
    </a>
    <div id="collapse'.$m['name'].'" class="collapse" aria-labelledby="headingAccounts"
        data-parent="#accordionSidebar">
        ';
        if ($_SESSION['admin_role'] != 2){
          echo '<div class="bg-white py-2 collapse-inner rounded">
              <h6 class="collapse-header">Pridať servis</h6>
              <a class="collapse-item" href="/adminlogin/modules/'.strtolower($m['path']).'/?t=1">Záručný/Predpredajný</a>
              <!--<a class="collapse-item" href="/adminlogin/modules/'.strtolower($m['path']).'/?t=P">ELECTROBETA servis</a>-->
          </div>';
        }
        echo '<div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Prehľad servisu</h6>
            <!--<a class="collapse-item" href="/adminlogin/modules/'.strtolower($m['path']).'/?t=1&p&limit=100&page=1">Záručný</a>
            <a class="collapse-item" href="/adminlogin/modules/'.strtolower($m['path']).'/?t=2&p&limit=100&page=1">Predpredajný</a>
            <a class="collapse-item" href="/adminlogin/modules/'.strtolower($m['path']).'/?t=4&p&limit=100&page=1">Pozáručný</a>-->
            <a class="collapse-item" href="/adminlogin/modules/'.strtolower($m['path']).'/?t=0&p&limit=100&page=1">Všetko</a>
        </div>
    </div>
</li>
';
?>
