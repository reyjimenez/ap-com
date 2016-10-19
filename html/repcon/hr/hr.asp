<%@ Language=VBScript %>

<!-- #INCLUDE FILE="headerfooter.inc" -->
<!-- #INCLUDE FILE="style.inc" -->
<%


Dim action
Dim path
dim root	: root = "hr\"



	action = LCase(Trim(Request("action")))
	path   = trim(request("path"))
	if len(trim(path)) = 0 then path = "forms\"

	call Form_Header("Altman Plants HR Forms Index")

	Select Case action
	     
	     Case Else:			Call Form_Display()
	     
	End Select
	
	Call Form_Footer()
	Response.End
	
%>





<%Sub Form_Display()
on error resume next
Dim prv_RS
Dim prv_SQL

Dim prv_EC
Dim prv_OC
Dim prv_CC
dim prv_RecCnt

dim f
dim d
dim fn
dim ext
dim img


	prv_EC = "#FFFFFF"
	prv_OC = "#E8E8E8"
	prv_CC = prv_EC
	prv_RecCnt = 0


	Set fso = CreateObject("Scripting.FileSystemObject")
	set f = fso.GetFolder(Request.ServerVariables("APPL_PHYSICAL_PATH") & root & path)


	'response.write( Request.ServerVariables("APPL_PHYSICAL_PATH") & root & path )
	




%>




    <table border="0" cellspacing="0" width="*">

    <tr>
      <td colspan="3"><font face="Tahoma" size="1" color="gray"><br>
IMPORTANT - To view the document, right mouse click on the Document Name and select "Save Targe AS...", make sure you remember where you save the document so that you can access it after the download is complete.<br><br>
</font></td>
    
    </tr>
    <tr>
      <td width="45" bgcolor="#C0C0C0"><font face="Tahoma" size="2" color="#FFFFFF">&nbsp</font></td>
      <td width="*" bgcolor="#C0C0C0"><font face="Tahoma" size="2" color="#FFFFFF">Document Name</font></td>
      <td width="*" bgcolor="#C0C0C0"><font face="Tahoma" size="2" color="#FFFFFF">Type</font></td>
    </tr>



     <tr>
      <td width="45"  align="center" bgcolor="<%=prv_CC%>"><font face="Tahoma" size="2" color="#000080"><img src="images/folder_back.jpg" border="0"></font></td>
      <td width="*"   align="left"   bgcolor="<%=prv_CC%>"><font face="Tahoma" size="2" color="#000080"><a href="<%=Request.ServerVariables("SCRIPT_NAME")%>?path=forms\">back</a></font></td>
      <td width="140" align="left"   bgcolor="<%=prv_CC%>"><font face="Tahoma" size="1" color="#000080"><%=d.type%></font></td>
    </tr>

    <%for each d in f.subfolders
      img = "images/folder.jpg"
    %>
    <tr>
      <td width="45"  align="center" bgcolor="<%=prv_CC%>"><font face="Tahoma" size="2" color="#000080"><img src="<%=img%>" border="0"></font></td>
      <td width="*"   align="left"   bgcolor="<%=prv_CC%>"><font face="Tahoma" size="2" color="#000080"><a href="<%=Request.ServerVariables("SCRIPT_NAME")%>?path=forms\<%=d.name%>\"><%=d.name%></a></font></td>
      <td width="140" align="left"   bgcolor="<%=prv_CC%>"><font face="Tahoma" size="1" color="#000080"></font></td>
    </tr>

    <%next%>



    <tr>
      <td width="45"  align="center" bgcolor="<%=prv_CC%>"><font face="Tahoma" size="2" color="#000080">&nbsp;</font></td>
      <td width="*"   align="left"   bgcolor="<%=prv_CC%>"><font face="Tahoma" size="2" color="#000080">&nbsp;</font></td>
      <td width="140" align="left"   bgcolor="<%=prv_CC%>"><font face="Tahoma" size="1" color="#000080">&nbsp;</font></td>
    </tr>


    <%for each fn in f.files%>
    <%ext = right(fn.name,3)
	select case ext
	   case "pdf": img = "images/pdf.jpg"
	   case "xls": img = "images/excel.jpg"
	   case "doc": img = "images/word.jpg"
	   case else:  img = "images/view.gif"
	end select
    %>
    <tr>
      <td width="45"  align="center" bgcolor="<%=prv_CC%>"><font face="Tahoma" size="2" color="#000080"><img src="<%=img%>" border="0"></font></td>
      <td width="*"   align="left"   bgcolor="<%=prv_CC%>"><font face="Tahoma" size="2" color="#000080"><a href="<%=path & fn.name%>"><%=left(fn.name, len(fn.name)-4)%></a></font></td>
      <td width="140" align="left"   bgcolor="<%=prv_CC%>"><font face="Tahoma" size="1" color="#000080"><%=fn.type%></font></td>
    </tr>
    <%next%>

    <tr>
      <td colspan="3" bgcolor="#C0C0C0"><font face="Tahoma" size="2" color="#FFFFFF">Item Count: <%=prv_RecCnt %></font></td>
    </tr>
   
  </table>

<%

	set fso = nothing


End Sub   ' End Sub Form_Display()%>


