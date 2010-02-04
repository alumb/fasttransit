
var height = 45;

function condence(routeName,table) {
	table.style.display = "none";

	var row=1;
	var col=0;
	
	//fix the titles
	while(col<table.rows[0].cells.length) {
		table.rows[0].cells[col].innerHTML = fixName(table.rows[0].cells[col].innerHTML);
		col++;
	}
	
	var newTable;
	var newRow;	
	var pageBreak;
	
	var tableNum = 0;
	
	while(row<table.rows.length) {

		if(!newTable || newRow>height) {
			if(newTable&&tableNum%2==0) {
				pageBreak = document.createElement('p');
				pageBreak.style.pageBreakBefore = "always";
				table.parentNode.appendChild(pageBreak);
			}
			//new table
			newTable = document.createElement('table');
			newTable.className = "PDA";
			newTable.border = "0";
			table.parentNode.appendChild(newTable);
			newTable.insertRow(0);
			newTable.rows[0].insertCell(0);
			newTable.rows[0].cells[0].innerHTML = routeName;
			newTable.rows[0].cells[0].colSpan = table.rows[0].cells.length;
			newTable.insertRow(1);
			newTable.rows[1].innerHTML = table.rows[0].innerHTML
			newRow = 2;
			tableNum++;
		}
		
		col=0;
		while(col<table.rows[row].cells.length)
		{
			if(!(/-/.test(table.rows[row].cells[col].innerHTML))) {
				newTable.insertRow(newRow);
				for(cell=0; cell<table.rows[row].cells.length; cell++) {
					newTable.rows[newRow].insertCell(cell);
					newTable.rows[newRow].cells[cell].innerHTML = table.rows[row].cells[cell].innerHTML.replace(/m/,"").replace(/\(.*\)/,"")
				}
				newRow++;
				break;
			}
			if(col+1 == table.rows[row].cells.length) {
				col=0;
				break;
			}
			else
				col++;
		}
		row++;
	}

}

function fixName(stopName) {
	stopName = stopName.replace(/<.*/ig,"");
	stopName = stopName.replace(/.*?Bound /ig,"");
	stopName = stopName.replace(/(Stn )?Bay.*/ig,"");
	stopName = stopName.replace(/,.*/ig,"");
	stopName = stopName.replace(/\sSt|\sAve|\sPky|\sDr/ig,"");
	stopName = stopName.replace(/\s[WENS]\s/ig," ");
	stopName = stopName.replace(/\s[WENS]$/ig,"");
	stopName = stopName.replace(/Wb\s|Sb\s|Nb\s|Eb\s|Fs\s/ig,"");
	stopName = stopName.replace(/^\s+/ig,"");
	stopName = stopName.replace(/\s+$/ig,"");
	return stopName;
}

function expand(table) {
	var row=0;
	while(row<table.rows.length) {
		if(table.rows[row].cells.count==1) {
			table.deleteRow(row);
			continue;
		}
		table.rows[row].style.display = "table-row";
	
		 row++;	
	}
}