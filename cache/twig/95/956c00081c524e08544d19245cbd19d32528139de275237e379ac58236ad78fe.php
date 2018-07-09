<?php

/* profile.html */
class __TwigTemplate_ae1755af4ab9d6b10d4eee120fd74c18fa0fca92b0949e81dc4b6adec7256786 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $this->displayBlock('body', $context, $blocks);
    }

    public function block_body($context, array $blocks = array())
    {
        // line 2
        echo "<h1>User List</h1>
<ul>
    <li><a href=\"";
        // line 4
        echo twig_escape_filter($this->env, $this->extensions['Slim\Views\TwigExtension']->pathFor("profile", array("name" => "ss")), "html", null, true);
        echo "\">Josh</a></li>
</ul>
";
    }

    public function getTemplateName()
    {
        return "profile.html";
    }

    public function getDebugInfo()
    {
        return array (  34 => 4,  30 => 2,  24 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% block body %}
<h1>User List</h1>
<ul>
    <li><a href=\"{{ path_for('profile', { 'name': 'ss' }) }}\">Josh</a></li>
</ul>
{% endblock %}", "profile.html", "/home/chenmt/Project/iLife/templates/profile.html");
    }
}
