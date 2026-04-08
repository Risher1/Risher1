<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class BootstrapButton
{
    private string $_strText;
    private string $_strType= "";
    private string $_strLink;
    private string $_strStyle;
      /**
     * Monte le composant de bouton Bootstrap sur la vue HTML
     * 
     * @param string $text Texte affiché dans le bouton
     * @param string $type Type de bouton : success, primary, warning, info, secondary, danger, light, dark
     * @param string $link Lien URL de la base <a>
     * @param bool $outlined Défini si le bouton est sans fond ou avec fond
     */
     public function mount(string $text, string $type, string $link, bool $outlined = false, string $style): void
    {
        $this->_strText = $text;        
        $this->_strLink = $link;
        $this->_strStyle = $style;

        if($outlined) {
            $this->_strType = 'outline-';
        }

        $this->_strType .= $type;
    }
    public function getText(): string 
    {
        return $this->_strText;
    }
    public function getLink(): string 
    {
        return $this->_strLink;
    }
    public function getType(): string 
    {
        return $this->_strType;
    }
      public function getStyle(): string 
    {
        return $this->_strStyle;
    }


}
