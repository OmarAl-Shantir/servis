function changePermission(input){
  var label = input;
  var button = input.getElementsByTagName("input")[0];
  var act_value = parseInt(button.value,10);
  const cls = ["btn-danger", "btn-info", "btn-warning", "btn-success", "btn-danger"];
  var text = ["Zakázané", "Čítanie", "Zápis", "Zmena", "Zakázané"];
  var next_value = [1, 2, 3, 0];
  label.classList.add(cls[next_value[act_value]]);
  label.classList.remove(cls[act_value]);
  button.setAttribute('value',next_value[act_value]);
  label.innerText = text[next_value[act_value]];

  label.appendChild(button);
}

function activeChange(input){
  var label = document.getElementById('active_label');
  if (input.checked == 0){
    label.innerHTML = "Neaktívny";
    label.classList.remove("btn-success");
    label.classList.add("btn-danger");

  } else {
    document.getElementById('active_label').innerHTML = "Aktívny";
    label.classList.remove("btn-danger");
    label.classList.add("btn-success");
  }
}

function checkPassword(){
  var new_pass = document.getElementById('new_password').value;
  var new_pass2 = document.getElementById('new_password2').value;
  if (new_pass == new_pass2){
    document.getElementById('message').innerHTML = 'OK';
  } else {
    document.getElementById('message').innerHTML = 'Heslá sa nezhodujú';
  }
}
