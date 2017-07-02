<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/22/2017
 * Time: 11:48 PM
 */

namespace App\Services;


class MenuBuilder
{
    /**
     * @var array
     */
    public $items;
    /**
     * @var AclManager
     */
    public $acl;
    /**
     * @var
     */
    public $frontMenu;

    /**
     * @param AclManager $acl
     */
    public function __construct(AclManager $acl)
    {
        $this->acl = $acl;
        $this->items = [
            'sidebar' => [],
            'topnav' => [],
            'user' => [],
        ];
    }

    /**
     * @param array $items
     * @param string $menu
     * @throws \Exception
     */
    public function addMenu(array $items, $menu = 'sidebar')
    {
        $this->setMenuItem($items, $menu);
    }

    /**
     * @throws \Exception
     */
    protected function setItems()
    {
        if (isset($this->frontMenu['sidebar'])) {
            $this->setMenuItem($this->frontMenu['sidebar'], 'sidebar');
        }
        if (isset($this->frontMenu['topnav'])) {
            $this->setMenuItem($this->frontMenu['topnav'], 'topnav');
        }
        if (isset($this->frontMenu['user'])) {
            $this->setMenuItem($this->frontMenu['user'], 'user');
        }
    }

    /**
     * @param $menu
     * @return array
     */
    protected function parseMenu($menu)
    {
        $tree = [];
        foreach ($menu as $key => $item) {
            if ($this->checkPermission($item)) {
                $tree[] = $this->parseMenuItem($item, $key);
            }
        }
        return $tree;
    }

    /**
     * @param $item
     * @return bool
     */
    protected function checkPermission($item)
    {
        $can_see = false;
        if ($item['permissions'] == false) {
            return true;
        }

        if (isset($item['permissions']) && is_array($item['permissions'])) {
            $can_see = $this->acl->canSee($item['permissions']);
        }
        return $can_see;
    }

    /**
     * @param $item
     * @param $key
     * @return string
     */
    protected function parseMenuItem($item, $key)
    {
        $hrefClass = isset($item['link']['class']) ? $item['link']['class'] : '';
        $hrefExtra = isset($item['link']['extra']) ? $item['link']['extra'] : '';
        $liclass = isset($item['li']['class']) ? $item['li']['class'] : '';
        $link = ($item['link']['link'] == '#') ? '#' : url($item['link']['link']);
        $return = '<li class="treeview ' . $liclass . '"><a href="' . $link . '" class="' . $hrefClass . '" ' . $hrefExtra . '>' . $item['link']['text'] . '</a>';
        if (isset($item['submenus'])) {
            $ulClass = isset($item['ul_submenu_class']) ? $item['ul_submenu_class'] : '';
            $return .= '<ul class="treeview-menu ' . $ulClass . '" id="' . $key . '">';
            foreach ($item['submenus'] as $key => $submenu) {
                if ($this->checkPermission($submenu)) {
                    $return .= $this->parseMenuItem($submenu, $key);
                }
            }
            $return .= "</ul>";
        }
        $return .= '</li>';
        return $return;
    }

    /**
     * @param array $items
     * @param string $group
     * @throws \Exception
     */
    public function setMenuItem(array $items = [], $group = 'sidebar')
    {
        if (!isset($this->items[$group])) {
            throw new \Exception("The group especified does not exists");
        }
        $this->items[$group] = array_merge($this->items[$group], $items);
    }

    /**
     * @param string $group
     * @param null $active
     * @return string
     * @throws \Exception
     */
    public function render($group = 'sidebar', $active = null)
    {
        $this->setActiveMenu($active);
        if (!isset($this->items[$group])) {
            throw new \Exception("The group specified does not exists");
        }
        $menu = collect($this->items[$group]);
        $string = "";
        $ordered = $menu->sortBy(function($item, $key){
            return $key;
        });
        foreach ($this->parseMenu($ordered) as $m) {
            $string .= $m;
        }
        return $string;
    }

    /**
     * @param $menu
     */
    protected function setSingleMenuActive($menu)
    {
        if (isset($this->items[$menu[0]][$menu[1]])) {
            if (isset($this->items[$menu[0]][$menu[1]]['li']['class'])) {
                $this->items[$menu[0]][$menu[1]]['li']['class'] = $this->items[$menu[0]][$menu[1]]['li']['class'] . ' active';
            } else {
                $this->items[$menu[0]][$menu[1]]['li']['class'] = " active";
            }
        }
    }

    /**
     * @param $menu
     * @param bool $submenu
     */
    protected function setSubMenuActive($menu, $submenu = false)
    {
        $this->setSingleMenuActive($menu);
        if (isset($this->items[$menu[0]][$menu[1]]['submenus'][$menu[2]])) {
            if (isset($this->items[$menu[0]][$menu[1]]['submenus'][$menu[2]]['li']['class'])) {
                $this->items[$menu[0]][$menu[1]]['submenus'][$menu[2]]['li']['class'] = $this->items[$menu[0]][$menu[1]]['submenus'][$menu[2]]['link']['class'] . ' active';
                $this->items[$menu[0]][$menu[1]]['ul_submenu_class'] = ' treeview-menu';
            } else {
                $this->items[$menu[0]][$menu[1]]['submenus'][$menu[2]]['li']['class'] = 'active';
                $this->items[$menu[0]][$menu[1]]['ul_submenu_class'] = ' treeview-menu';
            }
        }
    }

    /**
     * @param $name
     */
    public function setActiveMenu($name)
    {
        if (!empty($name)) {
            $menu = explode('.', $name);
            if (count($menu) === 2) {
                $this->setSingleMenuActive($menu);
            }
            if (count($menu) === 3) {
                $this->setSubMenuActive($menu);
            }
        }
    }

    /**
     * @param $menu
     */
    public function setMenu($menu)
    {
        $this->frontMenu = $menu;
        $this->setItems();
    }
}