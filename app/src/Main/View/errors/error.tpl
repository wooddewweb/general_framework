{if !$_config->application->errors->display_errors}

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		
		<title>Erro {$error_code}</title>
	</head>

	<body>
		<h1>
		{if $error_code == 404}
			Página não encontrada
		{else}
			Algo errado ocorreu
		{/if}
		</h1>

		<p>
			{if $error_code == 404}
				A página que você está procurando pode ter sido removida, teve o nome alterado ou está temporariamente indisponível.
			{else}
				A página que você está tentando acessar pode estar com um problema, dados errados podem ter sido inseridos anteriormente ou está temporariamente indisponível.
			{/if}
			<a href="/">Retorne à página inicial</a>
		</p>
	</body>
</html>


{else}
<pre>

{$exception->getMessage()}
	

{$exception->getTraceAsString()}

</pre>
{/if}