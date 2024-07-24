<?php

/**
 * Class ilExamOrgaSelectionExplorerGUI
 * @ilCtrl_IsCalledBy: ilExamOrgaSelectionExplorerGUI: ilPropertyFormGUI
 */
class ilExamOrgaSelectionExplorerGUI extends ilRepositorySelectorExplorerGUI
{
    protected ilSetting $settings;
    protected ilCtrl $ctrl;
    protected ilRbacSystem $rbacsystem;
    protected ilDBInterface $db;    

    /**
     * {@inheritdoc}
     */
    public function __construct(
        $a_parent_obj, 
        $a_parent_cmd,    
        $a_selection_gui = null,
        string $a_selection_cmd = "selectObject",
        string $a_selection_par = "sel_ref_id",
        string $a_id = "examorga_explorer_selection",
        string $a_node_parameter_name = "node_id"
    )
    {        
        /** @var \ILIAS\DI\Container $DIC */
        global $DIC;
        /**
        * Set the types that can be selected
        */
        $this->selectable_types = ['tst'];
        $this->settings = $DIC->settings();
        $this->ctrl = $DIC->ctrl();
        $this->rbacsystem = $DIC->rbac()->system();
        $this->db = $DIC->database();
        $this->setTypeWhiteList(array('root', 'cat', 'crs', 'grp', 'fold'));
        parent::__construct($a_parent_obj, $a_parent_cmd, $a_selection_gui, $a_selection_cmd, $a_selection_par, $a_id, $a_node_parameter_name);
    }    

    /**
     * Set the types that can be selected
     * @param array $a_types
     */
    public function setSelectableTypes(array $a_types): void
    {
        $this->selectable_types = $a_types;
        $this->setTypeWhiteList(array_merge(array('root', 'cat', 'crs', 'grp', 'fold'), $a_types));
    }

    /**
     * {@inheritdoc}
     */
    protected function isNodeSelectable($a_node): bool
    {
        if (!empty($this->selectable_types))
        {
            return in_array($a_node['type'], $this->selectable_types);
        }
        return true;
    }

    public function getNodeHref($a_node): string
	{
		return '';
	}
}