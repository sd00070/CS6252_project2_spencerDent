<?php
class Util
{
    /**
     * Get the root location of the project.
     * 
     * @return project_path
     */
    public static function getProjectPath()
    {
        return "/cs6252/projects/project2_spencerDent";
    }

    /**
     * Retrieves the action from the client's request.
     * It checks if the old request is still active first, then checks POST,
     * and finally it checks GET.
     * 
     * If it is unable to find the action, it returns an empty action String.
     * 
     * @return user_action
     */
    public static function getAction($old_action = '')
    {
        if ($old_action !== '') {
            return $old_action;
        }

        $new_action =
            filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?? '';
        return $new_action;
    }
}
