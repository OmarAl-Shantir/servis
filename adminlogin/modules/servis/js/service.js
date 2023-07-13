var adminRole;
var serviceData;
var type = null;
var limit = 100;
var offset = 0;
var filter = "";
var page = 1;
var allRecs;
var maxPages = 7;

function reDraw(sData){
  var tbodyEl = document.getElementById("servisData");
  tbodyEl.remove();
  tbodyEl = document.createElement("tbody");
  tbodyEl.setAttribute('id', 'servisData');
  var table = document.getElementById("dataTable");
  var rows = sData.length;
  var deleted = 0;
  var status;
  for(r = 0; r < rows; r++){
    switch(sData[r].id_stav_opravy){
      case 9:
        status = "inactive";
        break;
      case 16:
        status = "inactive";
        break;
      case 20:
        status = "inactive";
        break;
      case 21:
        status = "inactive";
        break;
      default:
        status = "active";
    }
    if (sData[r].deleted){
      status = "deleted";
    }

    var newRow = document.createElement("tr");
    newRow.setAttribute('class', status + " data");
    newRow.setAttribute('role', "row");

    var idCell = document.createElement("td");
    idCell.setAttribute('class', 'id sorting_1');
    idCell.textContent = sData[r].id_service_item;

    var servisnyListCell = document.createElement("td");
    if(sData[r].id_typ<4){
      var servisnyListA = document.createElement("a");
      servisnyListA.setAttribute('class', 'text-dark');
      servisnyListA.setAttribute('href', 'https://spares.eta.cz/b2b/reclamationServiceRecord?id=' + sData[r].servisny_list);
      servisnyListA.setAttribute('target', '_blank');
      servisnyListA.textContent = sData[r].servisny_list;
      servisnyListCell.appendChild(servisnyListA);
    } else {
      servisnyListCell.textContent = sData[r].servisny_list;
    }

    var refCell = document.createElement("td");
    refCell.textContent = sData[r].product_ref;

    var cpredajcuCell = document.createElement("td");
    cpredajcuCell.textContent = sData[r].cislo_reklamacie_predajcu;

    var vyrCisloCell = document.createElement("td");
    vyrCisloCell.textContent = sData[r].vyrobne_cislo;

    var vznikCell = document.createElement("td");
    vznikCell.textContent = convertDate(sData[r].datum_vzniku);

    var prijateCell = document.createElement("td");
    prijateCell.textContent = convertDate(sData[r].datum_prijatia);

    var typCell = document.createElement("td");
    typCell.textContent = sData[r].typ;

    var stavCell = document.createElement("td");
    stavCell.textContent = sData[r].stav_opravy;

    if ((sData[r].fullname == null) && (getAdminRole() != 2)){
      var prideleneCell = document.createElement("td");
      prideleneCell.setAttribute('class','text-center');
      var prevziatA = document.createElement("a");
      prevziatA.setAttribute('class', 'btn btn-primary');
      prevziatA.setAttribute('href', '/adminlogin/modules/servis/?t=A&p&a=' + sData[r].id_service_item);
      prevziatA.textContent = "Prevziať";
      prideleneCell.appendChild(prevziatA);
    } else {
      var prideleneCell = document.createElement("td");
      prideleneCell.textContent = sData[r].fullname;
    }

    var detailCell = document.createElement("td");
    detailCell.setAttribute('class', 'text-center');

    var detailA = document.createElement("a");
    detailA.setAttribute('class', 'btn btn-primary');
    detailA.setAttribute('href', '/adminlogin/modules/servis/detail.php?s=' + sData[r].id_service_item);
    detailA.textContent = "detaily";

    detailCell.appendChild(detailA);


    newRow.appendChild(idCell);
    newRow.appendChild(servisnyListCell);
    newRow.appendChild(refCell);
    newRow.appendChild(cpredajcuCell);
    newRow.appendChild(vyrCisloCell);
    newRow.appendChild(vznikCell);
    newRow.appendChild(prijateCell);
    newRow.appendChild(typCell);
    newRow.appendChild(stavCell);
    newRow.appendChild(prideleneCell);
    newRow.appendChild(detailCell);

    tbodyEl.appendChild(newRow);
  }
  table.appendChild(tbodyEl);

  pagination();
}

function convertDate(date){
  var dataPar = date.split("-");
  return dataPar[2]+"."+dataPar[1]+"."+dataPar[0];
}

function pagination(){
  var pager = document.getElementById("pagination");
  pager.remove();
  pager = document.createElement("ul");
  pager.setAttribute("id", "pagination");
  pager.setAttribute("class", "list-unstyled");
  var navPag = document.getElementById("navPagination");
  var i
  var maxP = Math.ceil(allRecs/limit);
  var pages = availablePages(maxP);

  if(pages[0] > 1){
    var ppl = document.createElement("li");
    ppl.setAttribute("class","page-item list-inline-item");
    var pplb = document.createElement("i");
    pplb.setAttribute("class","fa-solid fa-arrow-left");
    ppl.appendChild(pplb);
    pager.appendChild(ppl);
  }

  for(i=pages[0]; i <= pages[pages.length - 1]; i++){
    if (i == page){
      var ppl = document.createElement("li");
      ppl.setAttribute("class","page-item list-inline-item");
      var pplb = document.createElement("button");
      pplb.setAttribute("value",i);
      pplb.setAttribute("class","btn btn-primary active");
      pplb.textContent = i;
      ppl.appendChild(pplb);
    } else {
      var ppl = document.createElement("li");
      ppl.setAttribute("class","page-item list-inline-item");
      var pplb = document.createElement("button");
      pplb.setAttribute("value",i);
      pplb.setAttribute("onclick","toPage(this.value)");
      pplb.setAttribute("class","btn btn-primary");
      pplb.textContent = i;
      ppl.appendChild(pplb);
    }
    pager.appendChild(ppl);
  }
  if(pages[pages.length - 1] < maxP){
    var ppl = document.createElement("li");
    ppl.setAttribute("class","page-item list-inline-item");
    var pplb = document.createElement("i");
    pplb.setAttribute("class","fa-solid fa-arrow-right");
    ppl.appendChild(pplb);
    pager.appendChild(ppl);
  }
  /*if (i >= 3){
    var gtl = document.createElement("li");
    gtl.setAttribute("class","page-item list-inline-item");
    var gtli = document.createElement("input");
    gtli.setAttribute("type","number");
    gtli.setAttribute("class","form-control bg-light border-1");
    gtli.setAttribute("id", "goTo");
    gtl.appendChild(gtli);
    pager.appendChild(gtl);

    var gtl2 = document.createElement("li");
    gtl2.setAttribute("class","page-item list-inline-item");
    var btlb = document.createElement("button");
    btlb.setAttribute("onclick","toPage(document.getElementById("+'"goTo"'+").value)");
    btlb.setAttribute("class","btn btn-primary");
    btlb.textContent = "Choď na";
    gtl2.appendChild(btlb);
    pager.appendChild(gtl2);
  }*/
  navPag.appendChild(pager);
}

function toPage(p){
  page = p;
  offset = (p++ - 1) *limit;
  preLoad(type, limit, offset,filter);
}

function availablePages(lastPage) {
  var range= Math.floor(maxPages/2);
  if (lastPage<=maxPages){
    return [1,lastPage];
  } else {
    var l = Number(page) - range;
    var r = Number(page) + range;
    while (l < 1){
      l++;
      r++;
    }
    while (r > lastPage){
      l--;
      r--;
    }
    let arr = [];
    for (let i = l; i <= r; i++) {
      arr.push(i);
    }
    return arr;
  }
}

function preLoad(t, lim, off, fil) {
  if (t != null) type = t;
  if (lim != null) limit = lim;
  if (off != null) offset = off;
  if (fil != null) filter = fil;
  var request = $.ajax({
    type: "POST",
    url: 'ajax.php',
    dataType: 'json',
    data: {
      function: "serviceItems",
      typArg: type,
      limArg: limit,
      offArg: offset,
      filArg: filter
    }
  });
  request.done(function( msg ) {
    allRecs = msg[1];
    serviceData = msg[0];
    reDraw(serviceData);
  });

  request.fail(function( jqXHR, textStatus ) {
    //alert( "Request failed: " + jqXHR.status );
  });
}

function getAdminRole() {
  var request = $.ajax({
    type: "POST",
    url: 'ajax.php',
    dataType: 'json',
    data: {
      function: "getAdminRole"
    }
  });
  request.done(function( msg ) {
    adminRole = msg;
  });

  request.fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + jqXHR.status );
  });
}

function getZadavatel(){

}

function uploadImage(device) {
  var photo = document.getElementById("photo");
  var id_record = document.getElementById("id_record").value;
  var image_type = document.getElementById('image-type').value;

  var data=new FormData();
  //from inputs
  data.append('functionname','uploadPhotos');
  data.append(photo.name,photo.files[0]);
  data.append('id_record',id_record);
  data.append('image_type',image_type);

  var xmlhttp=new XMLHttpRequest()
  xmlhttp.open("POST", "uploadImages.php");
  xmlhttp.send(data);

  xmlhttp.onload = function(getUrl) {
    var image_type = document.getElementById('image-type').value;
    var res = JSON.parse(xmlhttp.response);
    var img_div = document.createElement("div");
    img_div.classList.add('col-sm-4', 'col-md-4');

    var a = document.createElement("a");
    a.setAttribute("href", res['file']);
    a.setAttribute("data-lightbox", 'set');

    var img = document.createElement("img");
    img.classList.add('col-sm-12', 'col-md-12');
    img.src = res['file'];

    if(device != 'mobile') {
      var btn = document.createElement("button");
      btn.classList.add('btn', 'btn-danger', 'col-md-12');
      btn.setAttribute("onclick", "deleteImage(this)");
      btn.setAttribute("type", "button");
      btn.setAttribute("data", res['id']);
      btn.innerHTML = "ODSTRANIŤ";
    }

    img_div.appendChild(a);
    a.appendChild(img);
    if(device != 'mobile') {
      img_div.appendChild(btn);
    }
    var image_docs = document.getElementById("image_docs_"+image_type);
    image_docs.appendChild(img_div);
  }
}

function deleteImage(img){
  var image_id = img.getAttribute('data');
  var data=new FormData();
  data.append('id_image',image_id);
  var xmlhttp=new XMLHttpRequest()
  xmlhttp.open("POST", "deleteImage.php");
  xmlhttp.send(data);
  xmlhttp.onload = function(){
    img.parentElement.remove();
    var res = xmlhttp.response;
  }
}

function searchRecord(){
  // Declare variables
  var input, filter, tr, title, author, isbn, id, i, titleValue, authorValue, isbnValue, idValue;
  input = document.getElementById('searchBox');
  filter = input.value.toUpperCase();
  var product_ref = recs.map(function(item){return item.product_ref;});
  tr = document.getElementsByClassName("data");
  var statusType = document.getElementById("statusType");
  var statusTypeValue = statusType.options[statusType.selectedIndex].text;

  // Loop through all list items, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    if (statusTypeValue != "Všetky"){
      t = tr[i].getElementsByClassName('stav')[0];
      tValue = t.textContent || t.innerText;
      if (tValue == statusTypeValue) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } else if(filter.substring(0,3) == "EBS"){
      id = tr[i].getElementsByClassName('id')[0];
      idValue = id.textContent || id.innerText;
      if (idValue.toUpperCase() == filter.substring(3)) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } else if(filter.substring(0,3).toUpperCase() == "NAY"){
      t = tr[i].getElementsByClassName('t')[0];
      tValue = t.textContent || t.innerText;
      if (tValue.toUpperCase() == "BLESKOVÁ VÝMENA") {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }else {
      r = tr[i].getElementsByClassName('r')[0];
      s = tr[i].getElementsByClassName('s')[0];
      t = tr[i].getElementsByClassName('t')[0];
      c = tr[i].getElementsByClassName('cislo_predajcu')[0];
      v = tr[i].getElementsByClassName('vyrobne_cislo')[0];
      cpi = tr[i].getElementsByClassName('cislo_prepravy_in')[0];
      cpo = tr[i].getElementsByClassName('cislo_prepravy_out')[0];
      rValue = r.textContent || r.innerText;
      sValue = s.textContent || s.innerText;
      tValue = t.textContent || t.innerText;
      cValue = c.textContent || c.innerText;
      vValue = v.textContent || v.innerText;
      cpiValue = cpi.textContent || cpi.innerText;
      cpoValue = cpo.textContent || cpo.innerText;
      if ((rValue.toUpperCase().indexOf(filter) > -1) || (sValue.toUpperCase().indexOf(filter) > -1) || (tValue.toUpperCase().indexOf(filter) > -1) || (cValue.toUpperCase().indexOf(filter) > -1) || (vValue.toUpperCase().indexOf(filter) > -1) || (cpiValue.toUpperCase().indexOf(filter) > -1) || (cpoValue.toUpperCase().indexOf(filter) > -1)) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function checkDeliveryIn() {
  var v = document.getElementById("deliveryIn");
  var value = v.options[v.selectedIndex].text;
  if (value == "SPS kuriér") {
    document.getElementById("vzdialenost").setAttribute("style", "display: none;");
    document.getElementById("hmotnost").setAttribute("style", "display: flex;");
    document.getElementById("cislo_prepravy").setAttribute("style", "display: flex;");
    document.getElementById("cislo_reklamacie_predajcu").setAttribute("style", "display: flex;");
  } else if (value == "iný prepravca na náklady reklamujúceho") {
    document.getElementById("vzdialenost").setAttribute("style", "display: none;");
    document.getElementById("hmotnost").setAttribute("style", "display: none;");
    document.getElementById("cislo_prepravy").setAttribute("style", "display: flex;");
    document.getElementById("cislo_reklamacie_predajcu").setAttribute("style", "display: flex;");
  } else if (value == "iný prepravca na náklady reklamujúceho") {
    document.getElementById("vzdialenost").setAttribute("style", "display: none;");
    document.getElementById("hmotnost").setAttribute("style", "display: none;");
    document.getElementById("cislo_prepravy").setAttribute("style", "display: flex;");
    document.getElementById("cislo_reklamacie_predajcu").setAttribute("style", "display: flex;");
  } else if (value == "Electrobeta HOME Servis") {
    document.getElementById("vzdialenost").setAttribute("style", "display: flex;");
    document.getElementById("hmotnost").setAttribute("style", "display: none;");
    document.getElementById("cislo_prepravy").setAttribute("style", "display: none;");
    document.getElementById("cislo_reklamacie_predajcu").setAttribute("style", "display: none;");
  } else {
    document.getElementById("vzdialenost").setAttribute("style", "display: none;");
    document.getElementById("hmotnost").setAttribute("style", "display: none;");
    document.getElementById("cislo_prepravy").setAttribute("style", "display: none;");
    document.getElementById("cislo_reklamacie_predajcu").setAttribute("style", "display: flex;");
  }
  preprava_copy();
}

function checkDeliveryOut() {
  var v = document.getElementById("deliveryOut");
  var value = v.options[v.selectedIndex].text;
  if (value == "SPS kuriér") {
    document.getElementById("vzdialenost").setAttribute("style", "display: none;");
    document.getElementById("hmotnost").setAttribute("style", "display: flex;");
    document.getElementById("cislo_prepravy").setAttribute("style", "display: flex;");
  } else if (value == "iný prepravca na náklady reklamujúceho") {
    document.getElementById("vzdialenost").setAttribute("style", "display: none;");
    document.getElementById("hmotnost").setAttribute("style", "display: none;");
    document.getElementById("cislo_prepravy").setAttribute("style", "display: flex;");
  } else if (value == "iný prepravca na náklady reklamujúceho") {
    document.getElementById("vzdialenost").setAttribute("style", "display: none;");
    document.getElementById("hmotnost").setAttribute("style", "display: none;");
    document.getElementById("cislo_prepravy").setAttribute("style", "display: flex;");
  } else if (value == "Electrobeta HOME Servis") {
    document.getElementById("vzdialenost").setAttribute("style", "display: flex;");
    document.getElementById("hmotnost").setAttribute("style", "display: none;");
    document.getElementById("cislo_prepravy").setAttribute("style", "display: none;");
  } else {
    document.getElementById("vzdialenost").setAttribute("style", "display: none;");
    document.getElementById("hmotnost").setAttribute("style", "display: none;");
    document.getElementById("cislo_prepravy").setAttribute("style", "display: none;");
  }
  preprava_copy();
}

function textSeparate() {
  var text = document.getElementById("pasteText").value;
  while (text.charAt(0) == '\t') {
    var text = text.substring(1);
  }
  var inputs = text.split('\t');
  document.getElementById("service_list").value = inputs[0];
  document.getElementById("product_ref").value = inputs[2];
  document.getElementById("shop").value = inputs[3];
  document.getElementById("info").value = inputs[4];
  document.getElementById("date_start").value = inputs[5];
  document.getElementById("date_adopt").value = inputs[6];
  checkRequirementsAuto();
}

function checkRequirementsAuto() {

  const button = document.getElementById('add_record_auto');
  var service_list = document.getElementById("service_list").value;
  var product_ref = document.getElementById("product_ref").value;
  var shop = document.getElementById("shop").value;
  var info = document.getElementById("info").value;
  var date_start = document.getElementById("date_start").value;
  var date_end = document.getElementById("date_adopt").value;
  if (service_list != "" && product_ref != "" && shop != "" && info != "" && date_start != "" && date_end != "") {
    button.disabled = false;
  } else {
    button.disabled = true;
  }
}

function checkRequirementsManual() {
  document.getElementById('new_cislo_prepravy').value = document.getElementById('cislo_prepravy').value;
  document.getElementById('new_hmotnost').value = document.getElementById('hmotnost').value;
  document.getElementById('new_vzdialenost').value = document.getElementById('vzdialenost').value;
  const button = document.getElementById('add_record_manual');
  /*  var service_list = document.getElementById("service_list").value;
    var product_ref = document.getElementById("product_ref").value;
    var shop = document.getElementById("shop").value;
    var info = document.getElementById("info").value;
    var date_start = document.getElementById("date_start").value;
    var date_end = document.getElementById("date_adopt").value;
    if(service_list != "" && product_ref != "" && shop != "" && info != "" && date_start != "" && date_end != ""){
      button.disabled = false;
    } else {
      button.disabled = true;
    }*/
}

function preprava_copy() {
  const button = document.getElementById('add_record_auto');
  var sposobPrepravy = document.getElementById('deliveryIn').options[document.getElementById('deliveryIn').selectedIndex].value;
  var cislo = document.getElementById('cislo_prepravy_input').value;
  var cislo_reklamacie_predajcu = document.getElementById('cislo_reklamacie_predajcu_input').value;
  var hmotnost = document.getElementById('hmotnost_input').value;
  var vzdialenost = document.getElementById('vzdialenost_input').value;
  document.getElementById('paste_delivery_in').value = sposobPrepravy;
  document.getElementById('paste_cislo_prepravy').value = cislo;
  document.getElementById('paste_cislo_reklamacie_predajcu').value = cislo_reklamacie_predajcu;
  document.getElementById('paste_hmotnost').value = hmotnost;
  document.getElementById('paste_vzdialenost').value = vzdialenost;
  document.getElementById('new_delivery_in').value = sposobPrepravy;
  document.getElementById('new_cislo_prepravy').value = cislo;
  document.getElementById('new_cislo_reklamacie_predajcu').value = cislo_reklamacie_predajcu;
  document.getElementById('new_hmotnost').value = hmotnost;
  document.getElementById('new_vzdialenost').value = vzdialenost;
  if(sposobPrepravy == 1){
    document.getElementById('paste_cislo_prepravy').setAttribute("pattern","70+[0-9]+");
    document.getElementById('new_cislo_prepravy').setAttribute("pattern","70+[0-9]+");
    document.getElementById('paste_cislo_prepravy').required = true;
    document.getElementById('new_cislo_prepravy').required = true;
  } else {
    document.getElementById('paste_cislo_prepravy').removeAttribute("pattern");
    document.getElementById('new_cislo_prepravy').removeAttribute("pattern");
    document.getElementById('paste_cislo_prepravy').required = false;
    document.getElementById('new_cislo_prepravy').required = false;
  }
  if(cislo.substr(0,1) == '%'){
    document.getElementById('cislo_prepravy_input').value = cislo.substr(1);
  }
}

/*function copyToRoot(location) {
  if(location == "intro"){
    var cislo_reklamacie_predajcu = document.getElementById('cislo_reklamacie_predajcu_intro').value;
    document.getElementById('cislo_reklamacie_predajcu_info').value = cislo_reklamacie_predajcu;
    document.getElementById('cislo_reklamacie_predajcu_root').value = cislo_reklamacie_predajcu;
  }
  if(location == "info"){
    var cislo_reklamacie_predajcu = document.getElementById('cislo_reklamacie_predajcu_info').value;
    var hmotnost = document.getElementById('hmotnost_info').value;
    var vzdialenost = document.getElementById('vzdialenost_info').value;
    document.getElementById('cislo_reklamacie_predajcu_intro').value = cislo_reklamacie_predajcu;
    document.getElementById('cislo_reklamacie_predajcu_root').value = cislo_reklamacie_predajcu;
    document.getElementById('hmotnost_root').value = hmotnost;
    document.getElementById('vzdialenost_root').value = vzdialenost;
  }
}*/

function searchICO(el,t) {
  var ico = el.value;
  var request = $.ajax({
    type: "POST",
    url: 'getFinstatData.php',
    dataType: 'json',
    data: {functionname: 'getDataByICO', arguments: ico},
  });
  request.done(function( msg ) {
    var r = JSON.stringify(msg);
    if(r == '{"result":"IČO nebolo nájdetené"}'){
      alert( "Zadané IČO nebolo nájdetené" );
    } else {
      if (confirm("Načítať údaje z FinStat-u?")) {
        if(typeof msg['result']['name'] !== 'undefined'){
          document.getElementById(t+'firma').value = msg['result']['name'];
        }
        if(typeof msg['result']['ico'] !== 'undefined'){
          document.getElementById(t+'ico').value = msg['result']['ico'];
        }
        if(typeof msg['result']['dic'] !== 'undefined'){
          document.getElementById(t+'dic').value = msg['result']['dic'];
        }
        if(typeof msg['result']['ic_dph'] !== 'undefined'){
          document.getElementById(t+'ic_dph').value = msg['result']['ic_dph'];
        }
        if(typeof msg['result']['adresa'] !== 'undefined'){
          document.getElementById(t+'adresa').value = msg['result']['adresa'];
        }
        if(typeof msg['result']['psc'] !== 'undefined'){
          document.getElementById(t+'psc').value = msg['result']['psc'];
        }
        if(typeof msg['result']['mesto'] !== 'undefined'){
          document.getElementById(t+'mesto').value = msg['result']['mesto'];
        }
      }
    }
  });

  request.fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + textStatus );
  });
  console.log(el);
  document.getElementById(el.dataset.type).value = "";
}

function zarucnaReklamaciaManual() {
  document.getElementById('manual_input').checked = 1;
  document.getElementById('auto_input').checked = 0;

  //ETA system
  document.getElementById('service_list').required = false;
  document.getElementById('product_ref').required = false;
  document.getElementById('shop').required = false;
  document.getElementById('date_start').required = false;
  document.getElementById('date_adopt').required = false;
  //predajca
  document.getElementById('predajca_adresa').required = true;
  document.getElementById('predajca_psc').required = true;
  document.getElementById('predajca_mesto').required = true;
  document.getElementById('predajca_ico').required = true;
  document.getElementById('predajca_firma').required = true;
  //objednavatel
  document.getElementById('obj_firma').required = true;
  document.getElementById('obj_adresa').required = true;
  document.getElementById('obj_meno').required = true;
  document.getElementById('obj_telefon').required = true;
  document.getElementById('obj_adresa').required = true;
  document.getElementById('obj_psc').required = true;
  document.getElementById('obj_mesto').required = true;
  //produkt
  document.getElementById('datum_kupy').required = true;
  document.getElementById('datum_vzniku').required = true;
  document.getElementById('datum_prijatia').required = true;
  document.getElementById('vyrobne_cislo').required = true;
  document.getElementById('pozadovane_riesenie').required = false;
  document.getElementById('orig_obal').required = false;
  document.getElementById('prislusenstvo').required = true;
  document.getElementById('stav').required = true;
  document.getElementById('chyba').required = true;
  document.getElementById('serv_vyjadrenia').required = true;

  document.getElementById('manual_form_input').setAttribute('style', 'display: block;');
  document.getElementById('auto_form_input').setAttribute('style', 'display: none;');
  if(document.getElementById('nema_ico').checked){
    nemaICO();
  } else {
    maICO();
  }
}

function zarucnaReklamaciaAuto() {
  document.getElementById('auto_input').checked = 1;
  document.getElementById('manual_input').checked = 0;

  //ETA system
  document.getElementById('service_list').required = true;
  document.getElementById('product_ref').required = true;
  document.getElementById('shop').required = true;
  document.getElementById('date_start').required = true;
  document.getElementById('date_adopt').required = true;
  //predajca
  document.getElementById('predajca_adresa').required = false;
  document.getElementById('predajca_psc').required = false;
  document.getElementById('predajca_mesto').required = false;
  document.getElementById('predajca_ico').required = false;
  document.getElementById('predajca_firma').required = false;
  //objednavatel
  document.getElementById('obj_firma').required = false;
  document.getElementById('obj_adresa').required = false;
  document.getElementById('obj_meno').required = false;
  document.getElementById('obj_telefon').required = false;
  document.getElementById('obj_adresa').required = false;
  document.getElementById('obj_psc').required = false;
  document.getElementById('obj_mesto').required = false;
  //produkt
  document.getElementById('datum_kupy').required = false;
  document.getElementById('datum_vzniku').required = false;
  document.getElementById('datum_prijatia').required = false;
  document.getElementById('vyrobne_cislo').required = false;
  document.getElementById('pozadovane_riesenie').required = false;
  document.getElementById('orig_obal').required = false;
  document.getElementById('prislusenstvo').required = false;
  document.getElementById('stav').required = false;
  document.getElementById('chyba').required = false;
  document.getElementById('serv_vyjadrenia').required = false;

  document.getElementById('manual_form_input').setAttribute('style', 'display: none;');
  document.getElementById('auto_form_input').setAttribute('style', 'display: block;');
}

function nemaICO() {
  document.getElementById('hladat_objednavatel_without_ico').setAttribute('style', 'display: flex');
  document.getElementById('hladat_objednavatel_with_ico').setAttribute('style', 'display: none');

  document.getElementById('nema_ico').checked = 1;
  document.getElementById('ma_ico').checked = 0;

  document.getElementById('div_obj_ico').setAttribute('style', 'display: none;');
  document.getElementById('div_obj_dic').setAttribute('style', 'display: none;');
  document.getElementById('div_obj_ic_dph').setAttribute('style', 'display: none;');
  document.getElementById('div_obj_firma').setAttribute('style', 'display: none;');

  //objednavatel
  document.getElementById('obj_firma').required = false;
}

function maICO() {
  document.getElementById('hladat_objednavatel_without_ico').setAttribute('style', 'display: none');
  document.getElementById('hladat_objednavatel_with_ico').setAttribute('style', 'display: flex');

  document.getElementById('ma_ico').checked = 1;
  document.getElementById('nema_ico').checked = 0;

  document.getElementById('div_obj_ico').setAttribute('style', 'display: flex;');
  document.getElementById('div_obj_dic').setAttribute('style', 'display: flex;');
  document.getElementById('div_obj_ic_dph').setAttribute('style', 'display: flex;');
  document.getElementById('div_obj_firma').setAttribute('style', 'display: flex;');

  //objednavatel
  document.getElementById('obj_firma').required = true;
}

function zarucnaReklamaciaTyp() {
  document.getElementById('predajca').setAttribute('style', 'display: block');
  document.getElementById('datum_kupy_div').setAttribute('style', 'display:flex');
  document.getElementById('datum_kupy').required = true;
  document.getElementById('predajca-objednavatel').setAttribute('style', 'display:block');
  document.getElementById('nema_ico').disabled = false;
  document.getElementById('zarucna_reklamacia').checked = 1;
  document.getElementById('predpredajna_reklamacia').checked = 0;
  document.getElementById('bleskova_vymena').checked = 0;

  document.getElementById('new_id_typ').value=1;
  document.getElementById('paste_id_typ').value=1;

  if(!document.getElementById('predajca_je_objednavatel').checked){
    document.getElementById('objednavatel').setAttribute('style', 'display: block');
    document.getElementById('obj_firma').required = true;
    document.getElementById('obj_adresa').required = true;
    document.getElementById('obj_meno').required = true;
    document.getElementById('obj_telefon').required = true;
    document.getElementById('obj_adresa').required = true;
    document.getElementById('obj_psc').required = true;
    document.getElementById('obj_mesto').required = true;
  } else {
    document.getElementById('obj_firma').required = false;
    document.getElementById('obj_adresa').required = false;
    document.getElementById('obj_meno').required = false;
    document.getElementById('obj_telefon').required = false;
    document.getElementById('obj_adresa').required = false;
    document.getElementById('obj_psc').required = false;
    document.getElementById('obj_mesto').required = false;
  }
}

function predpredajnaReklamaciaTyp() {
  document.getElementById('predajca').setAttribute('style', 'display: block');
  document.getElementById('objednavatel').setAttribute('style', 'display: none');
  document.getElementById('datum_kupy_div').setAttribute('style', 'display:none');
  document.getElementById('datum_kupy').required = false;
  document.getElementById('predajca-objednavatel').setAttribute('style', 'display:none');

  document.getElementById('nema_ico').disabled = true;
  document.getElementById('zarucna_reklamacia').checked = 0;
  document.getElementById('predpredajna_reklamacia').checked = 1;
  document.getElementById('bleskova_vymena').checked = 0;

  document.getElementById('new_id_typ').value=2;
  document.getElementById('paste_id_typ').value=2;

  document.getElementById('obj_firma').required = false;
  document.getElementById('obj_adresa').required = false;
  document.getElementById('obj_meno').required = false;
  document.getElementById('obj_telefon').required = false;
  document.getElementById('obj_adresa').required = false;
  document.getElementById('obj_psc').required = false;
  document.getElementById('obj_mesto').required = false;
}

function bleskovaVymenaTyp() {
  document.getElementById('sposob_2').selected = true;
  document.getElementById('predajca').setAttribute('style', 'display: block');
  document.getElementById('objednavatel').setAttribute('style', 'display: none');
  document.getElementById('datum_kupy_div').setAttribute('style', 'display:none');
  document.getElementById('datum_kupy').required = false;
  document.getElementById('predajca-objednavatel').setAttribute('style', 'display:none');

  document.getElementById('nema_ico').disabled = true;
  document.getElementById('zarucna_reklamacia').checked = 0;
  document.getElementById('predpredajna_reklamacia').checked = 0;
  document.getElementById('bleskova_vymena').checked = 1;

  document.getElementById('new_id_typ').value=3;
  document.getElementById('paste_id_typ').value=3;

  document.getElementById('obj_firma').required = false;
  document.getElementById('obj_adresa').required = false;
  document.getElementById('obj_meno').required = false;
  document.getElementById('obj_telefon').required = false;
  document.getElementById('obj_adresa').required = false;
  document.getElementById('obj_psc').required = false;
  document.getElementById('obj_mesto').required = false;
}

function chooseImageTab(evt, tabName) {
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("image-type-content");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("image-type-link");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  try {
    document.getElementById('image-type').value = tabName.slice(4);
  } catch {

  }
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}

//prepočet času na SJ
function sjcalculation(val) {
  output = document.getElementById('sj_value');
  if (val > 0) {
    output.innerHTML = Math.ceil(val / 15) + "SJ = (" + Math.ceil(val / 15) * 8 + " €)";
  } else {
    output.innerHTML = "";
  }
  prepareAction("Sadzobná jednotka", Math.ceil(val / 15), 8)
}

function prepareAction(ukon, mnozstvo, cena){
  ukon_input = document.getElementById('ukon_send');
  cena_input = document.getElementById('cena_send');
  mnozstvo_input = document.getElementById('mnozstvo_send');
  ukon_input.value = ukon;
  cena_input.value = cena;
  mnozstvo_input.value = mnozstvo;
}
//nastavenie minimálneho množstva
function minimalamount() {
  jc = document.getElementById('jc_input');
  mnozstvo = document.getElementById('mnozstvo_input');
  if (jc.value != "") {
    if (mnozstvo.value < 1) {
      mnozstvo.value = 1;
    }
  }
}

function calculationDoprava(){
  var ukon;
  if(document.querySelector('input[name="typDopravy"]:checked').value == "in"){
    ukon = "Import";
  } else {
    ukon = "Export";
  }
  var cena = document.getElementById('doprava_input').value;
  prepareAction(ukon, 1, cena);
}

function calculationPraca(){
  var ukon = "Cena práce";
  var cena = document.getElementById('cena_input').value;
  prepareAction(ukon, 1, cena);
}

function calculationDiel(){
  minimalamount();
  var ukon = document.getElementById('ukon_input').value;
  var mnozstvo = document.getElementById('mnozstvo_input').value;
  var cena = document.getElementById('jc_input').value;
  prepareAction(ukon, mnozstvo, cena);
}

function cenaDopravy(elm){
  elm = elm.getElementsByTagName("label")[0];
  elm.classList.remove("btn-outline-secondary");
  elm.classList.add('btn-success');

  if (document.getElementById('sadzobnaJednotka') !== null){
    elm = document.getElementById('sadzobnaJednotka').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }
  if (document.getElementById('nahradnyDiel') !== null){
    elm = document.getElementById('nahradnyDiel').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }
  if (document.getElementById('cenaPrace') !== null){
    elm = document.getElementById('cenaPrace').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }

  document.getElementById('nahradnyDiel').checked = 0;
  if (document.getElementById('cenaPrace') !== null){
    document.getElementById('cenaPrace').checked = 0;
  }

  if (document.getElementById('sadzobnaJednotka') !== null){
    document.getElementById('sadzobnaJednotka').checked = 0;
  }
  document.getElementById('nahradnyDiel').checked = 0;
  if (document.getElementById('cenaPrace') !== null){
    document.getElementById('cenaPrace').checked = 0;
  }
  document.getElementById('cenaDopravy').checked = 1;

  document.getElementById('ukon').setAttribute('style', 'display: none;');
  document.getElementById('jednotkova_cena').setAttribute('style', 'display: none;');
  document.getElementById('mnozstvo').setAttribute('style', 'display: none;');

  if (document.getElementById('sadzobnaJednotka') !== null){
    document.getElementById('cas').setAttribute('style', 'display: none;');
    document.getElementById('sj').setAttribute('style', 'display: none;');
  }

  if (document.getElementById('cenaPrace') !== null){
    document.getElementById('cena').setAttribute('style', 'display: none;');
  }
  document.getElementById('doprava').setAttribute('style', 'display: flex;');
}

function cenaPrace(elm) {
  elm = elm.getElementsByTagName("label")[0];
  elm.classList.remove("btn-outline-secondary");
  elm.classList.add('btn-success');

  if (document.getElementById('sadzobnaJednotka') !== null){
    elm = document.getElementById('sadzobnaJednotka').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }
  if (document.getElementById('nahradnyDiel') !== null){
    elm = document.getElementById('nahradnyDiel').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }
  if (document.getElementById('cenaDopravy') !== null){
    elm = document.getElementById('cenaDopravy').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }

  if (document.getElementById('sadzobnaJednotka') !== null){
    document.getElementById('sadzobnaJednotka').checked = 0;
  }
  document.getElementById('nahradnyDiel').checked = 0;
  document.getElementById('cenaPrace').checked = 1;
  document.getElementById('cenaDopravy').checked = 0;


  document.getElementById('ukon').setAttribute('style', 'display: none;');
  document.getElementById('jednotkova_cena').setAttribute('style', 'display: none;');
  document.getElementById('mnozstvo').setAttribute('style', 'display: none;');

  document.getElementById('cena').setAttribute('style', 'display: flex;');

  if (document.getElementById('sadzobnaJednotka') !== null){
    document.getElementById('cas').setAttribute('style', 'display: none;');
    document.getElementById('sj').setAttribute('style', 'display: none;');
  }
  document.getElementById('doprava').setAttribute('style', 'display: none;');
}

function sadzobnaJednotka(elm) {
  elm = elm.getElementsByTagName("label")[0];
  elm.classList.remove("btn-outline-secondary");
  elm.classList.add('btn-success');

  if (document.getElementById('cenaPrace') !== null){
    elm = document.getElementById('cenaPrace').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }
  if (document.getElementById('nahradnyDiel') !== null){
    elm = document.getElementById('nahradnyDiel').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }
  if (document.getElementById('cenaDopravy') !== null){
    elm = document.getElementById('cenaDopravy').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }

  document.getElementById('sadzobnaJednotka').checked = 1;
  document.getElementById('nahradnyDiel').checked = 0;
  if (document.getElementById('cenaPrace') !== null){
    document.getElementById('cenaPrace').checked = 0;
  }
  document.getElementById('cenaDopravy').checked = 0;

  document.getElementById('ukon').setAttribute('style', 'display: none;');
  document.getElementById('jednotkova_cena').setAttribute('style', 'display: none;');
  document.getElementById('mnozstvo').setAttribute('style', 'display: none;');

  document.getElementById('cas').setAttribute('style', 'display: flex;');
  document.getElementById('sj').setAttribute('style', 'display: flex;');

  document.getElementById('doprava').setAttribute('style', 'display: none;');
}

function nahradnyDiel(elm) {
  elm = elm.getElementsByTagName("label")[0];
  elm.classList.remove("btn-outline-secondary");
  elm.classList.add('btn-success');

  if (document.getElementById('cenaPrace') !== null){
    elm = document.getElementById('cenaPrace').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }
  if (document.getElementById('sadzobnaJednotka') !== null){
    elm = document.getElementById('sadzobnaJednotka').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }
  if (document.getElementById('cenaDopravy') !== null){
    elm = document.getElementById('cenaDopravy').parentElement;
    elm = elm.getElementsByTagName("label")[0];
    elm.classList.remove("btn-success");
    elm.classList.add('btn-outline-secondary');
  }

  if (document.getElementById('sadzobnaJednotka') !== null){
    document.getElementById('sadzobnaJednotka').checked = 0;
  }
  document.getElementById('nahradnyDiel').checked = 1;
  if (document.getElementById('cenaPrace') !== null){
    document.getElementById('cenaPrace').checked = 0;
  }
  document.getElementById('cenaDopravy').checked = 0;

  document.getElementById('ukon').setAttribute('style', 'display: flex;');
  document.getElementById('jednotkova_cena').setAttribute('style', 'display: flex;');
  document.getElementById('mnozstvo').setAttribute('style', 'display: flex;');

  if (document.getElementById('sadzobnaJednotka') !== null){
    document.getElementById('cas').setAttribute('style', 'display: none;');
    document.getElementById('sj').setAttribute('style', 'display: none;');
  }

  if (document.getElementById('cenaPrace') !== null){
    document.getElementById('cena').setAttribute('style', 'display: none;');
  }

  document.getElementById('doprava').setAttribute('style', 'display: none;');
}

function fillObjednavatel(obj, data){
  document.getElementById('obj_meno').value = data[obj]['meno'];
  document.getElementById('obj_ico').value = data[obj]['ico'];
  document.getElementById('obj_dic').value = data[obj]['dic'];
  document.getElementById('obj_ic_dph').value = data[obj]['ic_dph'];
  document.getElementById('obj_firma').value = data[obj]['firma'];
  document.getElementById('obj_adresa').value = data[obj]['ulica_cislo'];
  document.getElementById('obj_psc').value = data[obj]['psc'];
  document.getElementById('obj_mesto').value = data[obj]['mesto'];
  document.getElementById('obj_telefon').value = data[obj]['telefon'];
  document.getElementById('obj_mail').value = data[obj]['email1'];
  document.getElementById('id_zakaznik_input').value = obj;
}

function fillPredajca(obj, data){
  document.getElementById('predajca_meno').value = data[obj]['meno'];
  document.getElementById('predajca_ico').value = data[obj]['ico'];
  document.getElementById('predajca_dic').value = data[obj]['dic'];
  document.getElementById('predajca_ic_dph').value = data[obj]['ic_dph'];
  document.getElementById('predajca_firma').value = data[obj]['firma'];
  document.getElementById('predajca_adresa').value = data[obj]['ulica_cislo'];
  document.getElementById('predajca_psc').value = data[obj]['psc'];
  document.getElementById('predajca_mesto').value = data[obj]['mesto'];
  document.getElementById('predajca_telefon').value = data[obj]['telefon'];
  document.getElementById('predajca_mail').value = data[obj]['email1'];
  document.getElementById('id_zakaznik_input').value = obj;
}

function predajcaObjednavatel(){
  if (document.getElementById('predajca_je_objednavatel').checked == 1){
    document.getElementById('predajca_je_objednavatel_label').innerHTML = "Predajca je súčastne objednávatel";
    document.getElementById('objednavatel').setAttribute('style', 'display: none');
    document.getElementById('obj_firma').required = false;
    document.getElementById('obj_adresa').required = false;
    document.getElementById('obj_meno').required = false;
    document.getElementById('obj_telefon').required = false;
    document.getElementById('obj_adresa').required = false;
    document.getElementById('obj_psc').required = false;
    document.getElementById('obj_mesto').required = false;

  } else {
    document.getElementById('predajca_je_objednavatel_label').innerHTML = "Predajca nie je objednávatel";
    document.getElementById('objednavatel').setAttribute('style', 'display: block');
    document.getElementById('obj_firma').required = true;
    document.getElementById('obj_adresa').required = true;
    document.getElementById('obj_meno').required = true;
    document.getElementById('obj_telefon').required = true;
    document.getElementById('obj_adresa').required = true;
    document.getElementById('obj_psc').required = true;
    document.getElementById('obj_mesto').required = true;
  }
}

function predajca_manual(){
  document.getElementById('hladat_predajcu_input').value = "";
}

function objednavatel_manual(){
  document.getElementById('hladat_nema_ico').value = "";
  document.getElementById('hladat_ma_ico').value = "";
  document.getElementById('id_zakaznik_input').value = "";
}

function clearForm(t){
  console.log(t);
  document.getElementById(t+'firma').value = '';
  document.getElementById(t+'adresa').value = '';
  document.getElementById(t+'meno').value = '';
  document.getElementById(t+'ico').value = '';
  document.getElementById(t+'dic').value = '';
  document.getElementById(t+'ic_dph').value = '';
  document.getElementById(t+'telefon').value = '';
  document.getElementById(t+'adresa').value = '';
  document.getElementById(t+'psc').value = '';
  document.getElementById(t+'mesto').value = '';
  document.getElementById(t+'mail').value = '';
  document.getElementById(t+'telefon').value = '';
}

$(document).ready(function(e) {
  if (window.screen.width <= 300){
    a = document.getElementsByClassName('status_active')[0];
    x = a.getAttribute('x');
    p = x-60;
    if($('#svg').length){
      document.getElementById('svg').setAttribute('transform','scale(0.6,0.6)translate(-'+p+',-132)');
    }
  } else if (window.screen.width <= 600){
    a = document.getElementsByClassName('status_active')[0];
    x = a.getAttribute('x');
    p = x-60;
    if($('#svg').length){
      document.getElementById('svg').setAttribute('transform','scale(0.8,0.8)translate(-'+p+',-132)');
    }
  } else if (window.screen.width <= 1220){
    a = document.getElementsByClassName('status_active')[0];
    x = a.getAttribute('x');
    p = x-60;
    if($('#svg').length){
      document.getElementById('svg').setAttribute('transform','scale(1,1)translate(-'+p+',-132)');
    }
  } else {
    p = 52;
    if($('#svg').length){
      document.getElementById('svg').setAttribute('transform','scale(1,1)translate(-'+p+',-132)');
    }
  }
});
