var recs;
var maxP;
var id_service_item;
var servisny_list;
var product_ref;
var cislo_reklamacie_predajcu;
var vyrobne_cislo;
var datum_vzniku;
var datum_prijatia;
var id_typ;
var id_stav_opravy;
var id_vybavuje;


function getData(){
  var request = $.ajax({
    type: "POST",
    url: 'ajax.php',
    dataType: 'json',
    data: {
      function: "getRecordsData",
      data: getURLParameter('t')
    }
  });
  request.done(function( msg ) {
    recs = msg;
    save();
  });

  request.fail(function( jqXHR, textStatus ) {
    alert( "Request failed: " + jqXHR.status );
  });
  return recs;
}

function getURLParameter(sParam) {
    let sPageURL = window.location.search.substring(1);
    let sURLVariables = sPageURL.split('&');
    for (let i = 0; i < sURLVariables.length; i++)
    {
        let sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam)
        {
            return sParameterName[1];
        }
    }
}

function save(){
  id_service_item = recs.map(function(item){return item.id_service_item;});
  servisny_list = recs.map(function(item){return item.servisny_list;});
  product_ref = recs.map(function(item){return item.product_ref;});
  cislo_reklamacie_predajcu = recs.map(function(item){return item.cislo_reklamacie_predajcu;});
  vyrobne_cislo = recs.map(function(item){return item.vyrobne_cislo;});
  datum_vzniku = recs.map(function(item){return item.datum_vzniku;});
  datum_prijatia = recs.map(function(item){return item.datum_prijatia;});
  id_typ = recs.map(function(item){return item.id_typ;});
  id_stav_opravy = recs.map(function(item){return item.id_stav_opravy;});
  id_vybavuje = recs.map(function(item){return item.id_vybavuje;});
}

function toPage(page){
  let params = window.location.search.substring(1).split("&");
  for(i=0; i< params.length; i++){
    params[i] = params[i].split("=");
    if (params[i][0] == 'page'){
      params[i][1] = page;
    }
    params[i] = params[i].join("=");
  }
  let url = params.join("&");
  window.location.href = '?' + url;
  //window.location.search = $.query.set("page", page);
}

$(document).ready(function() {
  recs = getData();
  console.log(recs);
  let pager = $("#pagination");
  let limit = getURLParameter('limit');
  let page = getURLParameter('page');
  let i
  maxP = Math.ceil(recs.length/limit);
  for(i=1; i<=maxP; i++){
    if (i == page){
      pager.append("<li class='page-item'><button value='"+i+"' class='btn btn-primary active'>"+i+"</button></li>");
    } else {
      pager.append("<li class='page-item'><button value='"+i+"' class='btn btn-primary' onclick='toPage("+i+")'>"+i+"</button></li>");
    }
  }
  if (i >= 3){
    pager.append("<li class='page-item'><input type='number' class='form-control bg-light border-1' id='goTo'></li><li class='page-item'><button class='btn btn-primary' onclick='toPage(document.getElementById("+'"goTo"'+").value)'>Choď na</button></li>");
  }
});
