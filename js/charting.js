var datasets ={};

function getData(provider, stat)
{
	console.log("Tja");
	if (datasets[provider]==null)
	{
		console.log("fetching new data");
		$.ajax({
			type: "GET",
			url: "../database/getSteamData.php",
			data: {provider: provider, stat: stat},
			dataType: "json"
		}).done(function (data)
		{
			datasets[provider] = data;
			handleStat(data, stat)

		}).fail(function (jqXHR, textStatus, errorThrown)
		{
			console.log("AJAX call failed: " + textStatus + ", " + errorThrown);
		});
	}
	else
	{
		console.log("using stored data");
		handleStat(datasets[provider], stat);
	}
}

function handleStat(dataset, stat)
{
	switch (stat)
	{
		case "totalKills":
			result = extractDateData(dataset, stat);

			createLineChart(result[0], [result[1]]);
	}
}

function extractDateData(dataset, stat)
{
	var labels = [];
	var datapoints = [];
	for (var i of dataset)
	{
		var date = new Date(parseInt(i.TimeStamp) * 1000);
		labels.push(String(date.getDay()) + "/" + String(date.getMonth()) + " - " + String(date.getHours()) + ":" + String(date.getMinutes()));
		datapoints.push(parseInt(i[stat]));
	}
	return [labels, datapoints];
}

function createLineChart(labels, data)
{
	context = $("#canvas").get(0).getContext("2d");
	if (labels == 0)
	{
		context.font = "30px Arial";
		context.fillText("Data Missing", 100, 100);
		return;
	}
	if (labels == 1)
	{
		labels.push(labels[0]);
	}

	var i = 0;
	dataset =[];
	while (i < data.length)
	{
		if (data[i].length == 0)
		{
			context.font = "30px Arial";
			context.fillText("Data Missing", 100, 100);
			return;
		}
		if (data[i].length == 1)
		{
			data.push(datapoints[0]);
		}
		var colour;
		switch (i)
		{
			case 0: colour = "rgba (220, 0, 0, 0.2)";
				break;
			case 1: colour = "rgba (0, 220, 0, 0.2)";
				break;
			case 2: colour = "rgba (0, 0, 220, 0.2)";
				break;
			default: colour = "rgba ("+String(Math.random()*255)+","+String(Math.random()*255)+","+String(Math.random()*255)+", 0.2)";
		}

		var obj =
		{
			label: "Data",
			fillColor: colour,
			strokeColor: colour,
			pointColor: colour,
			pointStrokeColor: "#fff",
			pointHighlightFill: "#fff",
			pointHighlightStroke: colour,
			data: data[i]
		}

		dataset.push(obj);

		i++;
	}
	var graphData =
	{
		labels: labels,
		datasets: dataset
	};
	var chart = new Chart(context).Line(graphData, {bezierCurve: false});

}
