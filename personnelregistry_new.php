<h1>Add New Employee</h1>

<form name="newemployeeform" action="personnelregistry_new_doit.php" onSubmit="return formCheckNewEmployee()" method="post" enctype="multipart/form-data">
<input type="hidden" name="fsignaturedate" value="" />

<!-- Mellan-tabell start -->
<table id="infomiddle">
<tr>
<td width="166" valign="top">

<!-- Inre-tabell Foto start -->
<table id="photocol">
<tr>
<td id="photobox"><img src="photos/default.jpg" alt="Default Photo" width="164" /><br /><label class="file-upload"><input type="file" name="ffile" id="ffile"  /></label></td>
</tr>
<tr><td class="tablespacer"></tr>
<tr>
<td id="employeecode" style="color: #ffffff;">EMPLOYEE CODE: </b><br /><input type="text" name="femployeecode" id="femployeecode" size="7" maxlength="7" /></td>
</tr>
<tr><td class="tablespacer"></tr>
<tr>
<td id="securitylevel" style="color: #ffffff;">SECURITY ACCESS LEVEL: </b><br />
	<select class="select" name="fsecurityaccess" id="fsecurityaccess">
	<option value="C" selected="selected">C</option>
	<option value="B">B</option>
	<option value="A">A</option>
	</select>
</td>
</tr>
</table>
<!-- Inre-tabell Foto slut -->

</td>
<td width="135" valign="top">

<!-- Inre-tabell Variabler start -->
<table>
<tr>
<td class="variablecol" style="color: #ffffff;">NAME: &nbsp;</td>
</tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="variablecol" style="color: #ffffff;">DATE OF BIRTH: &nbsp;</td>
</tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="variablecol" style="color: #ffffff; padding-bottom: 5px;">SEX: &nbsp;</td>
</tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="variablecol" style="color: #ffffff; padding-bottom: 7px;">BLOOD TYPE: &nbsp;</td>
</tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="variablecol" style="color: #ffffff; padding-bottom: 10px;">HEIGHT: &nbsp;</td>
</tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="variablecol" style="color: #ffffff; padding-bottom: 10px;">WEIGHT: &nbsp;</td>
</tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="variablecol" style="color: #ffffff;">DEPARTMENT: &nbsp;</td>
</tr>
<tr>
<td class="variablecol" style="color: #ffffff;">
    <div style="margin-top: -7px;">RANK: &nbsp;</div>
</td>
</tr>
<tr><td class="tablespacer"></tr>
</table>
<!-- Inre-tabell Variabler slut -->

</td>
<td width="245" valign="top">

<!-- Inre-tabell Värden slut -->
<table>
<tr>
<td class="valuecol"><input type="text" name="fname" id="fname" size="20"  maxlength="255" /></td>
</tr>
<tr><td class="blackline"></td></tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="valuecol"><input type="date" name="fdateofbirth" id="fdateofbirth" size="20" maxlength="11" /></td>
</tr>
<tr><td class="blackline"></td></tr>
<tr><td class="tablespacer" style="padding-bottom: 4px;"></tr>
<tr>
<td class="valuecol">
	<select class="select" name="fsex" id="fsex">
	<option value="Male" selected="selected">Male</option>
	<option value="Female">Female</option>
	<option value="Other">Other</option>
	</select>
</td>
</tr>
<tr><td class="blackline"></td></tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="valuecol"><input type="text" name="fbloodtype" id="fbloodtype" size="7" maxlength="4"  /></td>
</tr>
<tr><td class="blackline"></td></tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="valuecol"><input type="text" name="fheight" id="fheight" size="7"  maxlength="7" /></td>
</tr>
<tr><td class="blackline"></td></tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="valuecol"><input type="text" name="fweight" id="fweight" size="7"  maxlength="7" /></td>
</tr>
<tr><td class="blackline"></td></tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="valuecol"><input type="text" name="fdepartment" id="fdepartment" size="20" value="Medical Data Department" maxlength="255"/></td>
</tr>
<tr><td class="blackline"></td></tr>
<tr><td class="tablespacer"></tr>
<tr>
<td class="valuecol"><input type="text" name="frank" id="frank" size="20" value="Junior Data Analyst" maxlength="255" /></td>
</tr>
<tr><td class="blackline"></td></tr>
<tr><td class="tablespacer"></tr>
</table>
<!-- Inre-tabell Värden slut -->

<td width="182" valign="top">

<!-- Inre-tabell Tom start -->
<!-- Inre-tabell Tom slut -->

</td>
</tr>
</table>
<!-- Mellan-tabell slut -->


<br /><p />
<h1>Background:</h1>
<textarea rows="5" cols="45" name="fbackground" id="fbackground" wrap="off"></textarea>
<p />
<h1>Strengths:</h1>
<textarea rows="5" cols="45" name="fstrengths" id="fstrengths" wrap="off"></textarea>
<p />
<h1>Weaknesses:</h1>
<textarea rows="5" cols="45" name="fweaknesses" id="fweaknesses" wrap="off"></textarea>
<p />
<input type="submit" class="button" value="Add New Employee" /><input class="button" type="reset" value="Reset Form" />
<p />
</form>

<script>
function formCheckNewEmployee() 
{
    // Hämta värdena från de obligatoriska fälten
    var employeeCode = document.getElementById("femployeecode").value.trim();
    var name = document.getElementById("fname").value.trim();

    // Kontrollera Employee Code
    if (employeeCode === "") {
        alert("Employee Code krävs!");
        document.getElementById("femployeecode").focus();
        return false; // Stoppar formuläret från att skickas
    }

    // Kontrollera Name
    if (name === "") {
        alert("Name krävs!");
        document.getElementById("fname").focus();
        return false; // Stoppar formuläret från att skickas
    }

    // Ingen kontroll görs på "ffile" — saknas fil sätter PHP default.jpg automatiskt.
    return true; // Godkänner och skickar formuläret
}
</script>