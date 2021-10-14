<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>CMDB :: Swagger UI</title>
	<link rel="stylesheet" type="text/css" href="/swagger-ui.css">
	<link rel="icon" type="image/png" href="/favicon.png" sizes="32x32" />
	<style>
		html {
			box-sizing: border-box;
			overflow: -moz-scrollbars-vertical;
			overflow-y: scroll;
		}

		*,
		*:before,
		*:after {
			box-sizing: inherit;
		}

		body {
			margin: 0;
			background: #fafafa;
		}
	</style>
</head>

<body>
	<div id="swagger-ui"></div>
	<script src="/swagger-ui-standalone-preset.js"></script>
	<script src="/swagger-ui-bundle.js"></script>
	<script>
		window.onload = function() {
			// Begin Swagger UI call region
			console.log(window.location.pathname);
			const ui = SwaggerUIBundle({
				url: window.location.protocol + "//" + window.location.hostname + ":" + window.location.port + "/swagger.json",
				dom_id: '#swagger-ui',
				deepLinking: true,
				presets: [
					SwaggerUIBundle.presets.apis,
					SwaggerUIStandalonePreset
				],
				layout: "StandaloneLayout"
			})
			// End Swagger UI call region
			window.ui = ui
		}
	</script>
</body>

</html>
