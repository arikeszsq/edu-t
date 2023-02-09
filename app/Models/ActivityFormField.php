<?php

namespace App\Models;


use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;

class ActivityFormField extends Model implements Sortable
{
    use ModelTree;

    protected $table = 'activity_form_fields';

    public function options()
    {
        return $this->hasMany(ActivityFormFieldOption::class,'activity_form_id','id' );
    }

    protected $fillable=['name'];
    protected $orderColumn = 'sort';
}
