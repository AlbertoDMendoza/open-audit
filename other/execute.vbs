'  Copyright 2022 Firstwave (www.firstwave.com)
'
'  This file is part of Open-AudIT.
'
'  Open-AudIT is free software: you can redistribute it and/or modify
'  it under the terms of the GNU Affero General Public License as published
'  by the Free Software Foundation, either version 3 of the License, or
'  (at your option) any later version.
'
'  Open-AudIT is distributed in the hope that it will be useful,
'  but WITHOUT ANY WARRANTY; without even the implied warranty of
'  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
'  GNU Affero General Public License for more details.
'
'  You should have received a copy of the GNU Affero General Public License
'  along with Open-AudIT (most likely in a file named LICENSE).
'  If not, see <http://www.gnu.org/licenses/>
'
'  For further information on Open-AudIT or for a license other than AGPL please see
'  www.firstwave.com or email sales@firstwave.com
'
' *****************************************************************************

' @package Open-AudIT
' @author Mark Unwin <mark.unwin@firstwave.com>
' @version   GIT: Open-AudIT_5.0.3
' @copyright Copyright (c) 2022, Firstwave
' @license http://www.gnu.org/licenses/agpl-3.0.html aGPL v3

' Used by PHP to spawn a new request without blocking user requests
' url, method and data are allowable parameters
' Will send a POST or GET depending on the method passed
' If data is pased, will send that with the POST
' eg:  ./execute.sh url=http://localhost/open-audit/index.php/input/queue/discoveries method=post

Option Explicit
dim url, objHTTP
dim objArgs, strArg, varArray
Set objArgs = WScript.Arguments
dim method
method = "GET"

for each strArg in objArgs
    if instr(strArg, "=") then
        varArray = split(strArg, "=")
        select case varArray(0)

            case "url"
                url = varArray(1)

            case "method"
                method = ucase(varArray(1))

        end select
    end if
next

if url <> "" then
    on error resume next
        Set objHTTP = WScript.CreateObject("MSXML2.ServerXMLHTTP.3.0")
        objHTTP.setTimeouts 5000, 5000, 5000, 120000
        objHTTP.SetOption 2, 13056
        if method = "GET" then
            objHTTP.Open method, url, True
        else
            objHTTP.Open method, url, False
            objHTTP.setRequestHeader "Content-Type","application/x-www-form-urlencoded"
            objHTTP.Send "data=1" + vbcrlf
        end if
    on error goto 0
end if