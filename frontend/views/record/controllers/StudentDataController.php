<?php

namespace frontend\views\record\controllers;

use common\models\PersonalInformation;
use common\models\StudentData;
use common\models\searches\StudentDataSearch;
use common\models\StudentGuardian;
use common\models\StudentInformation;
use common\models\StudentPlan;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;

class StudentDataController extends Controller
{

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new StudentDataSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $studentDataModel = new StudentData();
        $studentPersonalInformationModel = new PersonalInformation();
        $studentInformationModel = new StudentInformation();
        $studentPlanModel = new StudentPlan();
        $studentGuardianModel = new StudentGuardian();
        $guardianPersonalInformationModel = new PersonalInformation();

        if (Yii::$app->request->getIsPost()) {
            $postData = Yii::$app->request->post();

            if (
                $studentDataModel->load($postData) &&
                $studentPersonalInformationModel->load($postData, 'StudentPersonalInformation') &&
                $studentInformationModel->load($postData) &&
                $studentPlanModel->load($postData) &&
                $studentGuardianModel->load($postData) &&
                $guardianPersonalInformationModel->load($postData, 'GuardianPersonalInformation')
            ) {
                $isValid = $studentDataModel->validate();
                $isValid = $studentPersonalInformationModel->validate() && $isValid;
                $isValid = $studentInformationModel->validate() && $isValid;
                $isValid = $studentPlanModel->validate() && $isValid;
                $isValid = $studentGuardianModel->validate() && $isValid;
                $isValid = $guardianPersonalInformationModel->validate() && $isValid;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($isValid) {
                        $studentPersonalInformationModel->save(false);
                        $studentInformationModel->save(false);
                        $studentPlanModel->save(false);
                        $guardianPersonalInformationModel->save(false);

                        $studentGuardianModel->personal_information_id = $guardianPersonalInformationModel->id;
                        $studentGuardianModel->save(false);

                        $studentDataModel->personal_information_id = $studentPersonalInformationModel->id;
                        $studentDataModel->student_information_id = $studentInformationModel->id;
                        $studentDataModel->student_plan_id = $studentPlanModel->id;
                        $studentDataModel->guardian_id = $studentGuardianModel->id;

                        $studentDataModel->save(false);

                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $studentDataModel->id]);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error('Transaction failed: ' . $e->getMessage());
                }
            }
        }

        return $this->renderAjax('create', [
            'studentDataModel' => $studentDataModel,
            'studentPersonalInformationModel' => $studentPersonalInformationModel,
            'studentInformationModel' => $studentInformationModel,
            'studentPlanModel' => $studentPlanModel,
            'studentGuardianModel' => $studentGuardianModel,
            'guardianPersonalInformationModel' => $guardianPersonalInformationModel,
        ]);
    }

    public function actionUpdate($id)
    {
        $studentDataModel = $this->findModel($id);

        // Separate instances for student and guardian
        $studentPersonalInformationModel = PersonalInformation::findOne($studentDataModel->personal_information_id);
        $studentInformationModel = StudentInformation::findOne($studentDataModel->student_information_id);
        $studentPlanModel = StudentPlan::findOne($studentDataModel->student_plan_id);
        $studentGuardianModel = StudentGuardian::findOne($studentDataModel->guardian_id);
        $guardianPersonalInformationModel = PersonalInformation::findOne($studentGuardianModel->personal_information_id);

        $post = Yii::$app->request->post();

        if (
            $studentDataModel->load($post) &&
            $studentPersonalInformationModel->load($post, 'StudentPersonalInformation') &&
            $studentInformationModel->load($post) &&
            $studentPlanModel->load($post) &&
            $studentGuardianModel->load($post) &&
            $guardianPersonalInformationModel->load($post, 'GuardianPersonalInformation')
        ) {
            $isValid = $studentDataModel->validate();
            $isValid = $studentPersonalInformationModel->validate() && $isValid;
            $isValid = $studentInformationModel->validate() && $isValid;
            $isValid = $studentPlanModel->validate() && $isValid;
            $isValid = $studentGuardianModel->validate() && $isValid;
            $isValid = $guardianPersonalInformationModel->validate() && $isValid;

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($isValid) {
                    $studentPersonalInformationModel->save(false);
                    $studentInformationModel->save(false);
                    $studentPlanModel->save(false);
                    $guardianPersonalInformationModel->save(false);

                    $studentGuardianModel->personal_information_id = $guardianPersonalInformationModel->id;
                    $studentGuardianModel->save(false);

                    $studentDataModel->personal_information_id = $studentPersonalInformationModel->id;
                    $studentDataModel->student_information_id = $studentInformationModel->id;
                    $studentDataModel->student_plan_id = $studentPlanModel->id;
                    $studentDataModel->guardian_id = $studentGuardianModel->id;

                    $studentDataModel->save(false);

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $studentDataModel->id]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error('Transaction failed: ' . $e->getMessage());
            }
        }

        return $this->renderAjax('update', [
            'studentDataModel' => $studentDataModel,
            'studentPersonalInformationModel' => $studentPersonalInformationModel,
            'studentInformationModel' => $studentInformationModel,
            'studentPlanModel' => $studentPlanModel,
            'studentGuardianModel' => $studentGuardianModel,
            'guardianPersonalInformationModel' => $guardianPersonalInformationModel,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = StudentData::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
