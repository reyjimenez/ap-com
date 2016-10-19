<%Sub Form_Header(Title)

	if Session("WA") = TRUE then
		Call WA_Form_Header()
	else
		Call CA_Form_Header(Title)
	end if

End Sub%>

<%Sub Form_Footer()

	if Session("WA") = TRUE then
		Call WA_Form_Footer()
	else
		Call CA_Form_Footer()
	end if

End Sub%>



<%Sub CA_Form_Header(Title)%>

<html>
<head>
<title><%=Request.ServerVariables("SCRIPT_NAME")%></title>
</head>
<body>
<table border="0" width="100%">
	<tr>
		<td width="72%"><font face="Tahoma" size="4" color="#000080"><a href="<%=Request.ServerVariables("SCRIPT_NAME")%>?distloc=<%=request("distloc")%>&userid=<%=request("userid")%>"><img border="0" src="images/home.jpg"></a>&nbsp;<%=Title%></font></td>
		<td height="16" width="27%"><p align="right"><font face="Tahoma" size="1" color="#000080"><%=Request.ServerVariables("SCRIPT_NAME")%><br>Server Time: <%=now%></font></td>
	</tr>
</table>
<hr>

<%End Sub%>




<%Sub CA_Form_Footer()%>
<hr>
</body>
</html>
<%End Sub%>


<%Sub WA_Form_Header()%>
<HTML>
<HEAD>
<TITLE>Evolution</TITLE>
</HEAD>
<BODY>
<script type='text/javascript'>function Go(){return}</script>
<script type='text/javascript' src='menu/menu_vars.js'></script>
<script type='text/javascript' src='menu/<%=session("USERID")%>.js'></script>
<script type='text/javascript' src='menu/menu_com.js'></script>
<DIV align=left>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
  
  <TR>
    <TD width=161 height=20><IMG height=80 src="images/topleft.jpg" width=263 
      border=0></TD>
    <TD width="100%" background=images/topmiddle.jpg>&nbsp;</TD>
    <TD width=211 height=20><IMG height=80 src="images/topright.jpg" width=323 
      border=0></TD></TR>
  <TR>
    <TD bgColor=#cccc99 colSpan=3 height=20>&nbsp;</TD></TR>
  <TR>
    <TD colSpan=3 height=200><BR><FONT size=2>
    <!-- Page Content Follows -->
<%End Sub   ' end WA_App_Header()%>

<%Sub WA_Form_Footer()%>
      <!-- End Page Content -->
      </td>
    </tr>
    <tr>
      <td height="20" bgcolor="#cccc99" colspan="3">
      <table border="0" cellspacing="0" width="100%" height="1" cellpadding="0">
  <tr>
    <td width="22" height="1"><IMG height=20 src="images/bottom_left.gif" width=20 border=0></td>
    <td width="100%" height="1" bgcolor="#3333ff">&nbsp;</td>
    <td width="22" height="1"><IMG height=20 src="images/bottom_right.gif" width=20 border=0></td>
  </tr>
</table>

      </td>
    </tr>
  </table>
</div>
<br>
</body>
</html>
<%End Sub   'end WA_App_Footer()%>