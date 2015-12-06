class graph {
	constructor()
	{
		this.context = $("#canvas").get(0).getContext("2d");
	}
	getData(provider, stat)
	{
		$.get("../database/getSteamData.php",{provide: provider, stat:stat}, function (data)
		{
			if (data.length == 0)
			{
				console.log("1");
				context.font = "30px Arial";
				context.fillText("No Data Available", 100, 100);
				$.get("../database/ajaxUpdate.php", function ()
				{
					console.log("2;");
					graph();
					return;
				});
				return;
			}
			var labels = [];
			var kills = [];
			var deaths = [];
			for (var i of
			data
			)
			{
				date = new Date(parseInt(i.TimeStamp) * 1000);
				labels.push(String(date.getDate()) + " - " + String(date.getHours()) + ":" + String(date.getMinutes()));
				kills.push(parseInt(i.totalKills));
				deaths.push(parseInt(i.totalDeaths));
			}
			if (data.length == 1)
			{
				labels.push(labels[0]);
				kills.push(kills[0]);
				deaths.push(deaths[0]);
			}
			console.log(labels);
			console.log(kills);
			console.log(deaths);

			var graphData =
			{
				labels: labels,
				datasets: [
					{
						label: "Kills",
						fillColor: "rgba(0,70,220,0.2)",
						strokeColor: "rgba(0,70,220,1)",
						pointColor: "rgba(0,70,220,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(220,220,220,1)",
						data: kills
					},
					{
						label: "Deaths",
						fillColor: "rgba(222,0,0,0.2)",
						strokeColor: "rgba(222,0,0,1)",
						pointColor: "rgba(222,0,0,1)",
						pointStrokeColor: "#fff",
						pointHighlightFill: "#fff",
						pointHighlightStroke: "rgba(151,187,205,1)",
						data: deaths
					}
				]
			};
			var chart = new Chart(this.context).Line(graphData, {bezierCurve: false});


		}, "json");
	}
}

