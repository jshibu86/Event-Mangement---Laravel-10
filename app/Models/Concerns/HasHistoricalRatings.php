<?php
namespace App\Models\Concerns;
use App\Models\EventAgendaItem;
use Illuminate\Database\Eloquent\Relations\MorphMany;
trait HasHistoricalRatings {
    public function agendaItems(): MorphMany { return $this->morphMany(EventAgendaItem::class, 'item'); }
    public function getAverageRatingAttribute(): float { return (float) $this->agendaItems()->join('ratings','ratings.event_agenda_item_id','=','event_agenda_items.id')->avg('ratings.rating_value'); }
    public function getTimesPlayedAttribute(): int { return $this->agendaItems()->count(); }
}
