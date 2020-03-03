<?php

class UserRow extends Pix_Table_Row
{
    public function getDisplayName()
    {
        if ($data = json_decode($this->data)) {
            return $data->display_name;
        }
    }

    public function getImage()
    {
        if ($data = json_decode($this->data)) {
            return $data->image;
        }
    }
}

class User extends Pix_Table
{
    public function init()
    {
        $this->_name = 'user';
        $this->_primary = 'slack_id';
        $this->_rowClass = 'UserRow';

        $this->_columns['slack_id'] = array('type' => 'varchar', 'size' => 32);
        $this->_columns['account'] = array('type' => 'varchar', 'size' => 32);
        // 0 - member, 1 - proposer, 2 - admin 
        $this->_columns['type'] = array('type' => 'int');
        $this->_columns['created_at'] = array('type' => 'int');
        $this->_columns['logined_at'] = array('type' => 'int');
        $this->_columns['data'] = array('type' => 'text');

        $this->addIndex('account', array('account'), 'unique');
    }

    public static function parseUsers($users)
    {
        $user_accounts = array_map('trim', explode(',', trim($users)));
        $user_ids = array();
        foreach ($user_accounts as $account) {
            if ($user = User::find_by_account($account)) {
                $user_ids[] = $user->slack_id;
            }
        }
        return $user_ids;
    }

    public static function toUsersString($user_ids)
    {
        if (is_scalar($user_ids)) {
            $user_ids = explode(',', $user_ids);
        }
        $user_accounts = array();
        foreach ($user_ids as $user_id) {
            if ($user = User::find($user_id)) {
                $user_accounts[] = $user->account;
            }
        }
        return implode(',', $user_accounts);
    }
}
