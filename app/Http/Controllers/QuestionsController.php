<?php

namespace App\Http\Controllers;

use App\Http\Requests\AskQuestionRequest;
use App\Question;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Void_;

class QuestionsController extends Controller
{
    /**
     * QuestionsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth',['except'=>'index','show']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::with('user')->latest()->paginate();
        return view('questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $question = new Question;
        return view('questions.create', compact('question'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AskQuestionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AskQuestionRequest $request)
    {
        $request->user()->questions()->create($request->only('title', 'body'));
        return redirect()->route('questions.index')->with('success', 'Your question has been saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Question $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        $question->increment('views');
        return view('questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Question $question
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Question $question)
    {
        if ($this->authorize('update', $question)) {
            return view('questions.edit', compact('question'));
        }
        abort(403, 'Access Denied');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AskQuestionRequest $request
     * @param  \App\Question $question
     * @return \Illuminate\Http\Response
     */
    public function update(AskQuestionRequest $request, Question $question)
    {
        $question->update($request->only('title', 'body'));
        return redirect()->route('questions.index')->with('success', 'Your question has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question $question
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Question $question)
    {
        if ($this->authorize('delete',$question)) {
            $question->delete();
            return redirect()->route('questions.index')->with('success', 'Your question has been deleted');
        }
        abort(403);
    }
}
