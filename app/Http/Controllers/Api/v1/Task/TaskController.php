<?php

namespace App\Http\Controllers\Api\v1\Task;

use App\Http\Controllers\Controller;
use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /*
     * get task by pagination
     */
    public function getTaskWithPagination(): JsonResponse
    {
        $data = DB::table('tasks')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return Response()->json($data, HttpResponse::HTTP_OK);
    }

    /*
     * get all task in desc order
     */
    public function getAllTask(): JsonResponse
    {
        $data = DB::table('tasks')
            ->orderBy('id', 'desc')
            ->get();

        return Response()->json($data, HttpResponse::HTTP_OK);

    }

    /*
     * search task by title with like operator
     */
    public function searchTask($query): JsonResponse
    {
        $data = DB::table('tasks')
            ->where('title', 'like', $query . '%')
            ->orderBy('id', 'desc')
            ->get();

        return Response()->json($data, HttpResponse::HTTP_OK);

    }

    /*
     * get task by id
     */
    public function getTask($id): JsonResponse
    {
        $data = DB::table('tasks')
            ->where('id', '>', $id)
            ->first();

        return Response()->json($data, HttpResponse::HTTP_OK);

    }

    /*
     * get the task > then the passed arguments id
     */
    public function getTaskGreaterThenId($id): JsonResponse
    {

        $data = DB::table('tasks')
            ->where('id', '>', $id)
            ->orderBy('id', 'asc')
            ->get();

        return Response()->json($data, HttpResponse::HTTP_OK);

    }

    /*
     * create a task
     */
    public function store(Request $request): JsonResponse
    {

        $request->validate([
            'user_id' => 'required',
            'title' => 'required',
            'body' => 'required',
            'note' => 'required',
            'status' => 'required'
        ]);

        $data = new Task();
        $data->user_id = $request->user_id;
        $data->title = $request->title;
        $data->body = $request->body;
        $data->note = $request->note;
        $data->status = $request->status;
        $data->save();

        return Response()->json($data, HttpResponse::HTTP_CREATED);

    }

    /*
     * update a task
     */
    public function update(Request $request): JsonResponse
    {

        $request->validate([
            'id' => 'required',
            'user_id' => 'required',
            'title' => 'required',
            'body' => 'required',
            'status' => 'required'
        ]);

        $data = Task::where('id', '=', $request->id)->first();

        $data->user_id = $request->user_id;
        $data->title = $request->title;
        $data->body = $request->body;
        $data->note = $request->note;
        $data->status = $request->status;
        $data->save();

        return Response()->json($data, HttpResponse::HTTP_CREATED);
    }

    /*
     * delete task by id
     */
    public function destroy(Request $request): JsonResponse
    {

        $task = Task::where('id', '=', $request->get('id'))->first();

        if (!empty($task)) {

            if ($task->user_id == $request->get('user_id')) {
                $task->delete();

                return Response()->json($task, HttpResponse::HTTP_OK);
            }

            return response()->json(
                [
                    'error' => 'Task does not belong to you.'
                ], HttpResponse::HTTP_NOT_FOUND);

        }

        return response()->json(
            [
                'error' => 'Task does not exist.'
            ], HttpResponse::HTTP_NOT_FOUND);

    }
}
