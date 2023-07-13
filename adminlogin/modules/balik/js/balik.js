function toPackage(obj){
  obj.parentNode.removeChild(obj);
  var package = document.getElementById('package').getElementsByTagName('tbody')[0];
  obj.setAttribute("ondblclick", "outOfPackage(this)");
  obj.getElementsByTagName('input')[0].setAttribute("name", "inPackage[]");
  package.appendChild(obj);


}

function outOfPackage(obj){
  obj.parentNode.removeChild(obj);
  var package = document.getElementById('products').getElementsByTagName('tbody')[0];
  obj.setAttribute("ondblclick", "toPackage(this)");
  obj.getElementsByTagName('input')[0].setAttribute("name", "");
  package.appendChild(obj);
}

function datumPodania(obj){
  document.getElementById("datum_podania").value = obj.value;
  console.log(obj.value);
}
