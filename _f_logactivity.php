<?php
function logactivity($user, $date, $time, $activity, $object, $info, $category)
{
    global $conn;

    $stmt = $conn->prepare("INSERT INTO activitylog (user, `date`, `time`, activity, object, info, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $user, $date, $time, $activity, $object, $info, $category);
    $stmt->execute();
    $stmt->close();
}
?>