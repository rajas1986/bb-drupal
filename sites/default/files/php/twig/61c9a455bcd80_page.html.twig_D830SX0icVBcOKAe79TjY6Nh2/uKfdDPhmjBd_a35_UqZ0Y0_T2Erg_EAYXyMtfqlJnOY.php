<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* themes/custom/bb-app/templates/layout/page.html.twig */
class __TwigTemplate_a0362ca70388f6c56c531d6df51e6313c178035430d93b3fe83660987e67c478 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 45
        echo "<header class=\"header\">
  <div class=\"container\">
    <div class=\"row align-items-center\">
      <div class=\"col-2 col-logo\">
        <div class=\"logo\">
          <a href=\"";
        // line 50
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getUrl("<front>"));
        echo "\">
            <img width=\"190\" src=\"";
        // line 51
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($this->sandbox->ensureToStringAllowed(($context["base_path"] ?? null), 51, $this->source) . $this->sandbox->ensureToStringAllowed(($context["directory"] ?? null), 51, $this->source)), "html", null, true);
        echo "/images/logo.png\" alt=\"\">
          </a>
        </div>
      </div>
      <div class=\"col-10 col-right text-right\">
        <nav class=\"page_nav\">
          ";
        // line 57
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "navigation", [], "any", false, false, true, 57), 57, $this->source), "html", null, true);
        echo "
        </nav>
        <button type=\"button\" class=\"btn btn-humburger\"><span></span></button>
      </div>
    </div>
  </div>
</header>

  ";
        // line 73
        echo "
<main role=\"main\" class=\"main\">
  ";
        // line 75
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content_before", [], "any", false, false, true, 75), 75, $this->source), "html", null, true);
        echo "
  ";
        // line 76
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 76), 76, $this->source), "html", null, true);
        echo "
  ";
        // line 77
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content_after", [], "any", false, false, true, 77), 77, $this->source), "html", null, true);
        echo "
</main>

  ";
        // line 85
        echo "
  <footer class=\"footer\">
    <div class=\"main_footer\">
      <div class=\"container\">
        <div class=\"row\">
          ";
        // line 90
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer_first", [], "any", false, false, true, 90), 90, $this->source), "html", null, true);
        echo "
          <div class=\"col-md-3 column\">
          </div>
        </div>
      </div>
    </div>
    <div class=\"copyright text-center\">
      ";
        // line 97
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer", [], "any", false, false, true, 97), 97, $this->source), "html", null, true);
        echo "
    </div>
  </footer>

";
    }

    public function getTemplateName()
    {
        return "themes/custom/bb-app/templates/layout/page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  105 => 97,  95 => 90,  88 => 85,  82 => 77,  78 => 76,  74 => 75,  70 => 73,  59 => 57,  50 => 51,  46 => 50,  39 => 45,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/custom/bb-app/templates/layout/page.html.twig", "C:\\xampp\\htdocs\\bb-drupal\\themes\\custom\\bb-app\\templates\\layout\\page.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array();
        static $filters = array("escape" => 51);
        static $functions = array("url" => 50);

        try {
            $this->sandbox->checkSecurity(
                [],
                ['escape'],
                ['url']
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
