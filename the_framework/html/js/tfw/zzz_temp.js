var update_active=1;

//Añadido por C. González
function creaAjax(){
  var objetoAjax=false;
  if (typeof(XMLHttpRequest)!='undefined') {
  /* Compatibilidad con FireFox, Opera y cualquier otro BUEN navegador */
   try{
     objetoAjax = new XMLHttpRequest();
     }
  
   catch(e) { objetoAjax=false;}	
 } else{
	  try {
	   /*Para navegadores distintos a internet explorer*/
	   objetoAjax = new ActiveXObject("Msxml2.XMLHTTP");
	  } catch (e) {
	   try {
	     /*Para explorer*/
	     objetoAjax = new ActiveXObject("Microsoft.XMLHTTP");
	     } 
	     catch (E) {
	     objetoAjax = false;
	   }
  }}
  return objetoAjax;
}
function setMenu(url,var_,menu){
	var ajax = creaAjax();
	ajax.open('POST',url,true);
	ajax.onreadystatechange=function(){
		if(ajax.readyState==4){			
	  	if(ajax.status==200){
	  		//alert(ajax.responseText);
	  		var res = ajax.responseText.split('|');
  			if(res[0]=="1"){
   				if(document.getElementById(menu).style.display=="none"){
   					document.getElementById(menu).style.display="block";
   				}else{
   					document.getElementById(menu).style.display="none";
   				}
  			}
	  	}
	  	else{
	  		 alert("Error del sistema"+ajax.responseText);/*Si el estado es diferente de 200*/
	  	}	
		}
  }
	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	ajax.send(var_);
	//ajax.send(null);
	return;
}

function check_date_format(obj){
	var fecha=obj.value;
if(fecha!=""){
	var valida = /^(\d{2})\/(\d{2})\/(\d{4})$/.test(fecha);
	//var valida = compFormato(fecha);
 if( valida ){
 		 var c_fecha = new Date(RegExp.$3, parseFloat(RegExp.$2)-1, RegExp.$1);
   	 valida = (c_fecha.getDate()==RegExp.$1) && ((c_fecha.getMonth()+1)==RegExp.$2) && (c_fecha.getFullYear()==RegExp.$3);
 		 if( !valida){
 		 	 window.alert('La fecha introducida no es valida');
 		 	 obj.value="";
 		 	 obj.focus();
 			 return valida;
 		 }
 }
 else{
 		window.alert('La fecha introducida no es valida\nUtilice un formato dd/mm/aaaa');
    obj.value="";
    obj.focus();
    return valida;
 }
}
}
//Fin añadido C. González