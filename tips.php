<?php

public function getTaskLog() {
		$this->autoRender = false;
		$this->loadModel('TaskLog');

		// get values here
		$taskIds = $this->request->data['taskIdToday'];
		$taskIdsArr = json_decode($taskIds);

		$taskLogs = $this->TaskLog->find('all', array(
			'conditions' => array(
				'TaskLog.task_id IN' => $taskIdsArr
			),
			'joins' => array(
				array(
					'table' => 'Tasks',
					'alias' => 'Task',
					'type' => 'INNER',
					'conditions' => array(
						'TaskLog.task_id = Task.id'
					)
				)
			),
			'fields' => array('TaskLog.*', 'Task.name', 'SUM(TaskLog.task_time) as sum_task_time'),
			'order' => 'TaskLog.task_id ASC',
			'group' => 'TaskLog.task_id'
		));

		$arrTaskLog = array();
		foreach ( $taskLogs as $taskLog ) {
			$tmpArr = $taskLog['TaskLog'];
			$tmpArr['sum_task_time'] = $taskLog[0]['sum_task_time'];
			$tmpArr['name'] = $taskLog['Task']['name'];
			$arrTaskLog[] = $tmpArr;
		}

		return json_encode(array(
			'error' => false,
			'data' => $arrTaskLog
		));
	}


$('#send-cw').click(function (e) {
				$.ajax({
					method: "POST",
					url: "http://localhost:8888/cakephp/cakephp2x/tasks/getTaskLog",
					data: { taskIdToday: JSON.stringify(taskIdToday) }
				}).done(function( res ) {
					var results = JSON.parse(res);
					console.log(results);
					$(results.data).each(function( index, value ) {
						console.log(index);
						console.log(value);
						$('#task-log').append('<tr>' +
							'<td>' + value.task_id + '</td>' +
							'<td>' + value.name + '</td>' +
							'<td>' + value.sum_task_time + '</td>' +
							'</tr>');
					});
				});
			});