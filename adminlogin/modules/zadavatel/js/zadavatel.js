function searchRecord(){
  // Declare variables
  var input, filter, tr, title, author, isbn, id, i, titleValue, authorValue, isbnValue, idValue;
  input = document.getElementById('searchBox');
  filter = input.value.toUpperCase();
  tr = document.getElementsByClassName("data");
  // Loop through all list items, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    id = tr[i].getElementsByClassName('id')[0];
    f = tr[i].getElementsByClassName('firma')[0];
    m = tr[i].getElementsByClassName('meno')[0];
    e = tr[i].getElementsByClassName('email')[0];
    ico = tr[i].getElementsByClassName('ico')[0];
    dic = tr[i].getElementsByClassName('dic')[0];
    idValue = id.textContent || id.innerText;
    fValue = f.textContent || f.innerText;
    mValue = m.textContent || m.innerText;
    eValue = e.textContent || e.innerText;
    icoValue = ico.textContent || ico.innerText;
    dicValue = dic.textContent || dic.innerText;
    if ((idValue.toUpperCase().indexOf(filter) > -1) || (fValue.toUpperCase().indexOf(filter) > -1) || (mValue.toUpperCase().indexOf(filter) > -1) || (eValue.toUpperCase().indexOf(filter) > -1) || (icoValue.toUpperCase().indexOf(filter) > -1) || (dicValue.toUpperCase().indexOf(filter) > -1)) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }
  }
}
