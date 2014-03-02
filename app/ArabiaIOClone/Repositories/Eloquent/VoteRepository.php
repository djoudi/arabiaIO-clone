<?php

namespace ArabiaIOClone\Repositories\Eloquent;

use ArabiaIOClone\Repositories\Eloquent\AbstractRepository;
use ArabiaIOClone\Repositories\VoteRepositoryInterface;
use \Vote;
/**
 * Description of VoteRepository
 *
 * @author Hichem MHAMED
 */
class VoteRepository extends AbstractRepository implements VoteRepositoryInterface
{
    public function __construct(Vote $vote)
    {
        parent::__construct($vote);
        $this->model = $vote;
    }
    
    public function tryDownvotePost($post, $user)
    {
        $userVote = $post->votes()
                        ->with('users')
                        ->where("user_id",'=',$user->id)
                        ->first();
        
        if($userVote) // user voted on post, should update
        {
            $newVoteValue = max(-1,$userVote->vote - 1);

            $userVote->vote = $newVoteValue;
            $userVote->save();
            
            $post->sumvotes = $post->votes()->sum('vote');
            $post->save();
            
            return $post->sumvotes;


        }else // first time user vote on post should create
        {
            $newVote = $post->votes()->create([ 'user_id' => $user->id,
                                                'vote' => -1,
                                            ]);
            $post->sumvotes = $post->votes()->sum('vote');
            $post->save();
            
            return $post->sumvotes;
        }
    }
    
    public function tryUpvotePost($post, $user)
    {
        $userVote = $post->votes()
                        ->with('users')
                        ->where("user_id",'=',$user->id)
                        ->first();
        
        if($userVote) // user voted on post, should update
        {
            $newVoteValue = min(1,$userVote->vote + 1);

            $userVote->vote = $newVoteValue;
            $userVote->save();
            
            $post->sumvotes = $post->votes()->sum('vote');
            $post->save();
            
            return $post->sumvotes;


        }else // first time user vote on post should create
        {
            $newVote = $post->votes()->create([ 'user_id' => $user->id,
                                                'vote' => 1,
                                            ]);
            $post->sumvotes = $post->votes()->sum('vote');
            $post->save();
            
            return $post->sumvotes;
        }
    }
    
}

?>