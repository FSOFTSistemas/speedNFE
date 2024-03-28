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
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
    <link rel="shortcut icon" href="{{ asset('site/img/logo.png') }}" />

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
                <li><a href="https://w.app/RSq5E3" target="_blank" class="button">Saiba mais</a></li>
                <li><a href="{{Route('login')}}" target="_blank" class="button" style="margin-left: 40px;">Ja tem conta ? Faça Login</a></li>

            </ul>

        </section>

        <!-- Main -->
        <section id="main" class="container">


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
                        <p>Conte com nossa equipe de suporte técnico altamente qualificada para ajudá-lo em qualquer
                            etapa do processo, garantindo uma experiência tranquila e livre de problemas.</p>
                    </section>
                </div>
                <div class="features-row">
                    <section>
                        <span class="icon solid major fa-cloud accent4"></span>
                        <h3>Armazenamento na Nuvem</h3>
                        <p>Seus dados são salvos de forma segura na nuvem, proporcionando acesso conveniente e seguro de
                            qualquer dispositivo, a qualquer momento.</p>
                    </section>
                    <section>
                        <span class="icon solid major fa-lock accent5"></span>
                        <h3>Conformidade Legal Garantida e Segurança</h3>
                        <p>Fique tranquilo sabendo que nosso sistema está sempre atualizado com as últimas
                            regulamentações fiscais, garantindo total conformidade legal em suas emissões de NF-e.
                            </br>Protegemos seus dados com os mais altos padrões de segurança, utilizando criptografia
                            avançada e medidas de proteção contra ameaças cibernéticas.</p>
                    </section>
                </div>
            </section>




            <section class="box special">
                <header class="major">
                    <h2>Emita Notas Fiscais Facilmente</h2>
                    <p>O SpeedNFE é um sistema online que permite que você emita notas fiscais de qualquer lugar, a
                        qualquer hora, utilizando apenas um smartphone ou tablet. O sistema é fácil de usar e acessível,
                        ideal para empresas de todos os portes.</p>
                </header>
                <span class="image featured"><img src="{{ asset('css/images/tela.png') }}" alt="" /></span>
            </section>

        </section>

        <div class="row" id="planos">

                <h2>Conheça Nossos Planos</h2>
                <div class="row" id="card-planos">

                    <div class="col-4 col-12-narrower">

                        <section class="box special">
                            <span class="image featured"><img src="{{ asset('css/images/banner.jpg') }} " alt="" style="height: 400px;" /></span>
                            <h3>Plano Básico</h3>
                            <p>Emissao de 1 nota teste grátis<br>
                                Cadastro de até 12 clientes<br>
                                Emissão de até 10 notas p/mês<br>
                                Cadastro de até 10 produtos<br>
                                Emissão de NFe<br>
                                Suporte via email
                            </p>
                            <ul class="actions special">
                                <li><a href="https://w.app/nWIw45" class="button alt">Saiba mais</a></li>
                            </ul>
                        </section>

                    </div>
                    <div class="col-4 col-12-narrower">

                        <section class="box special">
                            <span class="image featured"><img src="{{ asset('css/images/conceito-de-relatorio-de-graficos-visuais-de-grafico-de-negocios.jpg') }}" alt="" style="height: 400px;" /></span>
                            <h3>Plano Advanced</h3>
                            <p>Emissao de 1 nota teste grátis<br>
                                Cadastro de até 20 clientes<br>
                                Emissão de até 20 notas p/mês<br>
                                Cadastro de até 20 produtos<br>
                                Emissão de NFe<br>
                                Suporte via whatsApp<br>
                            </p>
                            <ul class="actions special">
                                <li><a href="https://w.app/XcUVa7" class="button alt">Saiba mais</a></li>
                            </ul>
                        </section>

                    </div>
                    <div class="col-4 col-12-narrower">

                        <section class="box special">
                            <span class="image featured"><img src="{{ asset('css/images/trabalhadores-de-escritorio-usando-graficos-de-financas.jpg') }}" alt="" style="height: 400px;" /></span>
                            <h3>Plano Premium</h3>
                            <p>Emissao de 1 nota teste grátis<br>
                                Cadastro ilimitado de clientes<br>
                                Emissão de até 40 notas p/mês<br>
                                Cadastro ilimitado de produtos<br>
                                Emissao de NFe e MDFe<br>
                                Suporte via whatsApp<br>
                            </p>
                            <ul class="actions special">
                                <li><a href=" https://w.app/6f4TY9" class="button alt">Saiba mais</a></li>
                            </ul>
                        </section>

                    </div>
                </div>
            </div>

            <section id="main" class="container" style="margin-top: 10px">
                <section class="box special">
                    <header class="major">
                        <h2>Sobre Nós</h2>
                        <h4>Emita Notas Fiscais Facilmente</h4>
                        <p style="font-size: 18px">
                            A SpeedNFe é uma plataforma inovadora que oferece uma solução completa para emissão e gestão de
                            NFe
                            e NFC-e. Somos uma empresa especializada em soluções fiscais, com mais de 10 anos de experiência
                            no
                            mercado, e estamos comprometidos em oferecer aos nossos clientes a melhor experiência possível.
                        </p>


                        <h3 style="margin-top: 25px">Nossos Diferenciais</h3>

                        <ul style="border-top: 2px solid rgba(0, 0, 0, 0.2); padding-top: 30px">
                            <li><strong> Fácil de usar: </strong> Plataforma intuitiva e amigável, ideal para usuários de todos os níveis.</li>
                            <li><strong> Completa: </strong> Oferecemos todos os recursos que você precisa para gerenciar suas notas fiscais,
                                desde
                                a criação até a entrega.</li>
                            <li><strong> Confiável:</strong> Somos uma empresa certificada pela SEFAZ, garantindo a segurança e confiabilidade
                                de
                                seus dados fiscais.</li>
                            <li><strong> Suporte especializado:</strong> Contamos com uma equipe de especialistas em NFe e NFC-e à disposição
                                para
                                te ajudar.</li>
                        </ul>

                        <strong>
                            <p>
                                A SpeedNFe é a solução ideal para empresas de todos os portes que desejam:
                            </p>
                        </strong>

                        <ul>
                            <li>Agilizar a emissão de notas fiscais;</li>
                            <li>Reduzir custos;</li>
                            <li>Aumentar a produtividade;</li>
                            <li>Ter mais segurança e confiabilidade em seus processos fiscais.</li>
                        </ul>
                        </strong>
                    </header>
                </section>
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
                    <li>&copy; FSOFT SISTEMAS. All rights reserved.</li>
                    <li>Design: <a href="https://f-softsistemas.com.br/">FSOFT SISTEMAS</a></li>
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
