<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Traits\HasResponsible;
use App\Models\Traits\HasHistories;
use App\Models\Traits\HasComments;
use App\Models\Traits\HasAudit;
use App\Models\Traits\HasAttachments;
use App\Filters\ActionFilter;

class Action extends Model
{
    use HasAudit,
        HasResponsible,
        HasComments,
        HasAttachments,
        HasHistories;

    protected $table = 'action';
    
    protected $guarded = ['id'];

    protected $defaultFilter = ActionFilter::class;
    
    // Relationships

    public function type()
    {
        return $this->belongsTo(ActionType::class, 'action_type_id');
    }

    public function analyzer()
    {
        return $this->belongsTo(User::class, 'analyzer_id');
    }

    public function system()
    {
        return $this->belongsTo(System::class, 'system_id');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function analysis()
    {
        return $this->hasOne(ActionAnalysis::class, 'action_id');
    }

    public function verification()
    {
        return $this->hasOne(ActionVerification::class, 'action_id');
    }

    public function result()
    {
        return $this->hasOne(ActionResult::class, 'action_id');
    }

    public function tasks()
    {
        return $this->hasMany(ActionTask::class, 'action_id');
    }

    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'action_complaint', 'action_id', 'complaint_id');
    }

    public function completedTasks()
    {
        return $this->tasks()
            ->whereNotNull('completed_at');
    }

    public function expiredTasks()
    {
        return $this->tasks()
            ->whereNull('completed_at')
            ->whereDate('expiration_date', '<=', Carbon::today());
    }

    // Attributes

    public function getCodeAttribute()
    {
        return sprintf("%s-%04d", $this->type->prefix, $this->number);
    }

    public function getIsCorrectiveAttribute()
    {
        return $this->action_type_id == 1;
    }

    public function getDurationAttribute()
    {
        if ($this->result) {
            $days = $this->created_at->diffInDays($this->result->completed_at);
        } else {
            $days = $this->created_at->diffInDays();
        }

        if ($days) {
            return $days . ' día' . ($days > 1 ? 's' : '');
        }

        return null;
    }

    // Functions

    public function getInvolvedUsersAttribute()
    {
        $users = User::whereExists(function ($query) {
            $query->select($query->raw(1))
                ->from('action')
                ->where('action.id', $this->id)
                ->whereRaw('(`action`.`responsible_id` = `user`.`id` or `action`.`analyzer_id` = `user`.`id`)')
                ->union(function ($query) {
                    $query->select($query->raw(1))
                        ->from('action_task')
                        ->where('action_task.action_id', $this->id)
                        ->whereRaw('`action_task`.`responsible_id` = `user`.`id`');
                });
            ;
        });

        return $users->get();
    }
}
