
function getPlaceholder(paramsSel, paramsId, paramsMark) {
  if(paramsSel) {
    $(paramsId).prepend("<option selected disabled>"+paramsMark+"</option>");
  }
}

