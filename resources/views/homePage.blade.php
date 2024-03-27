<!DOCTYPE HTML>
<!--
	Alpha by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>SpeedNFE</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="{{asset('css/main.css')}}" />




	</head>
	<body class="landing is-preload">
		<div id="page-wrapper">

			<!-- Header -->
				<header id="header" class="alt">
					<h1><a href="{{ route('homePage') }}">SpeedNFE</a></h1>
					<nav id="nav">
						<ul>
							<li><a href="{{ route('homePage') }}">Home</a></li>
							<li><a href="{{ route('home') }}" class="button">Entrar</a></li>
						</ul>
					</nav>
				</header>

			<!-- Banner -->
				<section id="banner">
					<h2>SpeedNFE</h2>
					<p>Simplifique a emissão de notas fiscais</p>
                    <p>Emita notas fiscais de qualquer lugar, sem a necessidade de um computador</p>
					<ul class="actions special">
						<li><a href="{{ route('home') }}" class="button primary">Inscrever-se</a></li>
						<li><a href="#" class="button">Saiba mais</a></li>
					</ul>
				</section>

			<!-- Main -->
			<section id="main" class="container">
					<section class="box special">
						<header class="major">
							<h2>Emita Notas Fiscais Facilmente</h2>
							<p>O SpeedNFE é um sistema online que permite que você emita notas fiscais de qualquer lugar, a qualquer hora, utilizando apenas um smartphone ou tablet. O sistema é fácil de usar e acessível, ideal para empresas de todos os portes.</p>
						</header>
						<span class="image featured"><img src="{{ asset('css/images/tela.png') }}" alt="" /></span>
					</section>

					<section class="box special features">
                        <h3 style="text-align: center;"> Serviços</h3>
						<div class="features-row">
							<section>
                            <span class="icon solid major fa-file-alt accent2"></span>
								<h3>Emissão de notas fiscais</h3>
								<p>NFe (Nota Fiscal Eletrônica) </br>
                                    NFC-e (Nota Fiscal de Consumidor Eletrônica) </br>
                                    CT-e (Conhecimento de Transporte Eletrônico) </br>
                                    MDF-e (Manifesto Eletrônico de Documentos Fiscais) </br>
                                    NFS-e (Nota Fiscal de Serviços Eletrônica)</p>
							</section>
							<section>
                            <span class="icon solid major fa-headset accent3"></span>
								<h3>Suporte Técnico Especializado</h3>
								<p>Conte com nossa equipe de suporte técnico altamente qualificada para ajudá-lo em qualquer etapa do processo, garantindo uma experiência tranquila e livre de problemas.</p>
							</section>
						</div>
						<div class="features-row">
							<section>
								<span class="icon solid major fa-cloud accent4"></span>
								<h3>Armazenamento na Nuvem</h3>
								<p>Seus dados são salvos de forma segura na nuvem, proporcionando acesso conveniente e seguro de qualquer dispositivo, a qualquer momento.</p>
							</section>
							<section>
								<span class="icon solid major fa-lock accent5"></span>
								<h3>Conformidade Legal Garantida e Segurança</h3>
								<p>Fique tranquilo sabendo que nosso sistema está sempre atualizado com as últimas regulamentações fiscais, garantindo total conformidade legal em suas emissões de NF-e. </br>Protegemos seus dados com os mais altos padrões de segurança, utilizando criptografia avançada e medidas de proteção contra ameaças cibernéticas.</p>
							</section>
						</div>
					</section>

					<div class="row">
						<div class="col-6 col-12-narrower">

							<section class="box special">
								<span class="image featured"><img src="{{ asset('css/images/pic02.jpg') }} " alt="" /></span>
								<h3>Plano Básico</h3>
								<p>Integer volutpat ante et accumsan commophasellus sed aliquam feugiat lorem aliquet ut enim rutrum phasellus iaculis accumsan dolore magna aliquam veroeros.</p>
								<ul class="actions special">
									<li><a href="#" class="button alt">Saiba mais</a></li>
								</ul>
							</section>

						</div>
						<div class="col-6 col-12-narrower">

							<section class="box special">
								<span class="image featured"><img src="{{ asset('css/images/pic03.jpg') }}" alt="" /></span>
								<h3>Plano Intermediário</h3>
								<p>Integer volutpat ante et accumsan commophasellus sed aliquam feugiat lorem aliquet ut enim rutrum phasellus iaculis accumsan dolore magna aliquam veroeros.</p>
								<ul class="actions special">
									<li><a href="#" class="button alt">Saiba mais</a></li>
								</ul>
							</section>

						</div>
                        <div class="col-6 col-12-narrower">

							<section class="box special">
								<span class="image featured"><img src="{{ asset('css/images/pic03.jpg') }}" alt="" /></span>
								<h3>Plano Premium</h3>
								<p>Integer volutpat ante et accumsan commophasellus sed aliquam feugiat lorem aliquet ut enim rutrum phasellus iaculis accumsan dolore magna aliquam veroeros.</p>
								<ul class="actions special">
									<li><a href="#" class="button alt">Saiba mais</a></li>
								</ul>
							</section>

						</div>
					</div>

				</section>


			<!-- CTA -->
				<section id="cta">

					<h2>Solicite já uma demonstração</h2>
					<p>Experimente uma Demonstração Gratuita do Nosso Sistema</p>

					<form>
						<div class="row gtr-50 gtr-uniform">
							<div class="col-8 col-12-mobilep">
								<input type="email" name="email" id="email" placeholder="Email" />
							</div>
							<div class="col-4 col-12-mobilep">
								<input type="submit" value="Solicitar" class="fit" />
							</div>
						</div>
					</form>

				</section>

			<!-- Footer -->
				<footer id="footer">
					<ul class="icons">
						<li><a href="https://f-softsistemas.com.br/" target="_blank" class="icon brands fa-facebook-f"><span class="label">Facebook</span></a></li>
						<li><a href="https://www.instagram.com/fsoft_sistemas/" target="_blank" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
						<li><a href="https://github.com/fsoftsistemas" target="_blank" class="icon brands fa-github"><span class="label">Github</span></a></li>
						<li><a href="https://g.co/kgs/CjviXRU" target="_blank" class="icon brands fa-google-plus"><span class="label">Google+</span></a></li>
					</ul>
					<ul class="copyright">
						<li>&copy; FSOFT SISTEMAS. All rights reserved.</li><li>Design: <a href="https://f-softsistemas.com.br/">FSOFT SISTEMAS</a></li>
					</ul>
				</footer>

		</div>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.dropotron.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

	</body>
</html>
