var maxt;

function showTime() {

   maxt = setTimeout("showTime()",1000);
   myDate = new Date();
   
   hours   = myDate.getHours();
   minutes = myDate.getMinutes();
   seconds = myDate.getSeconds();

   if (hours < 10)   hours   = "0" + hours;
   if (minutes < 10) minutes = "0" + minutes;
   if (seconds < 10) seconds = "0" + seconds;

   document.getElementById("maxtime").innerHTML = hours+":"+minutes+":"+seconds;
}

function slides(id, path,ses) {
	var xmlHttp;	
	document.getElementById("slides").innerHTML = '<div style="text-align: center">Sedang proses... silakan tunggu!!</div>';

	try	{
		xmlHttp=new XMLHttpRequest();
	}
	catch (e) {
		try	{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
			catch (e)
			{
				alert("Browser Anda tidak mendukung AJAX!");
				return false;
			}
		}
	}
		
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			document.getElementById("slides").innerHTML = xmlHttp.responseText;
		}
	}
	xmlHttp.open("GET", path+"/"+id+"/"+ses,true);
	xmlHttp.send(null);
}



function conf() {
	$('.ask').jConfirmAction();
}


function setIn(id) {
	var z=$(id).attr("temp");
	if ($(id).val()==z) $(id).val('');
    $(id).removeClass("inputstyle").addClass("focusField");
}

function setOut(id){
	$(id).removeClass("focusField").addClass("inputstyle");
	var z=$(id).attr("temp");
	if ($(id).val() == "") $(id).val(z).css("color","#ccc");
	else $(id).css("color","#000");
}

function notif(data,link) {
	$(link).html('<div class=notif>'+data+'</div><div style=\"clear: both\"></div>');
}

function caution(data,link) {
	$(link).html('<div class=alert>'+data+'</div><div style=\"clear: both\"></div>');
}

function renderTime() {
	var currentTime = new Date();
	var diem = "AM";
	var h = currentTime.getHours();
	var m = currentTime.getMinutes();
        var s = currentTime.getSeconds();
            setTimeout('renderTime()',1000);
        if (h === 0) {
                    h = 12;
        } else if (h > 12) { 
                h = h - 12;
                diem="PM";
        }
        if (h < 10) {
                h = "0" + h;
        }
        if (m < 10) {
                m = "0" + m;
        }
        if (s < 10) {
                s = "0" + s;
        }
        $('#jam').html(h + ":" + m + ":" + s + " " + diem);
           
}

function date_time(id)
{
//        date = new Date;
//        year = date.getFullYear();
//        month = date.getMonth();
//        months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'Jully', 'August', 'September', 'October', 'November', '12');
//        d = date.getDate();
//        day = date.getDay();
//        days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
//        h = date.getHours();
//        if(h<10)
//        {
//                h = "0"+h;
//        }
//        m = date.getMinutes();
//        if(m<10)
//        {
//                m = "0"+m;
//        }
//        s = date.getSeconds();
//        if(s<10)
//        {
//                s = "0"+s;
//        }
//        result = d+'/'+months[month]+'/'+year+' '+h+':'+m+':'+s;
//        document.getElementById(id).value = result;
//        setTimeout('date_time("'+id+'");','1000');
//        return true;
}

$(function() {
   
      var ribbonheight=$(".ribbon").height()+30;
      $(".main").css('margin-top',ribbonheight);
      
      $("#timepanel a,#userpanel a").click(function() {
              if($(this).next(".subpanel").is(':visible')){
                      $(this).next(".subpanel").hide();
                      $("#mainpanel li a").removeClass('active');
              } else { 
                      $(".subpanel").hide();
                      $(this).next(".subpanel").toggle();
                      $("#mainpanel li a").removeClass('active');
                      $(this).toggleClass('active');
              }
              return false;
      });

      $(document).click(function() {
              $(".subpanel").hide();
              $("#mainpanel li a").removeClass('active');
      });
      
      $('.subpanel').click(function(e) { 
              e.stopPropagation();
      });
		
      $(".mainmodule li").click(function() {
            
            var no_split=$(this).attr("class");
            var no=no_split.split("-");

            $(".assetnav").hide();
            $('.leafribbon').show();
            $('.showleaf').hide();
            $("#box-"+no[1]).show();
            $(".mainmodule li").css({background: 'none', color: '#fff', margin: '0'});
            $(this).css({background: '#efefef', color: '#000', margin: '2px 0 0 0'});
            var leafheight=($(".leafribbon").height())+40;
            $(".main").css('margin-top',leafheight+'px')
      });
   
      
      $("ul.asseticon li").click(function(){
          var url=$(this).find("div").attr("url");
          window.location=url;
      });
      
      $('.hideleaf').click(function(){
         $('.leafribbon').hide();
         $('.showleaf').show();
         $('.main').css('margin-top','35px');
      });
      
      $('.showleaf').click(function(){
         $('.leafribbon').show();
         $('.showleaf').hide();
         var leafheight=($(".leafribbon").height())+40;
         $(".main").css('margin-top',leafheight+'px');
      });
      
});