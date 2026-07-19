@Composable
fun PickupStatusCard(
    status: String,
    currentStep: Int
) {

    val steps = listOf(
        "Submitted",
        "Confirmed",
        "Assigned",
        "Pickup",
        "Completed"
    )

    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(dimen_16),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = dimen_2)
    ) {

        Column(
            modifier = Modifier.padding(dimen_18)
        ) {
            Row(
                verticalAlignment = Alignment.CenterVertically
            ) {

                Box(
                    modifier = Modifier
                        .size(dimen_42)
                        .background(
                            color = MidLightWhitish,
                            CircleShape
                        ),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        Icons.Default.LocalShipping,
                        null,
                        tint = DarkGreen
                    )
                }

                Spacer(Modifier.width(dimen_12))

                Column(
                    modifier = Modifier.weight(1f)
                ) {

                    Textview(
                        text = status,
                        size = font_14,
                        color = Blackish,
                        fontWeight = FontWeight.Medium
                    )

                    Spacer(Modifier.height(dimen_2))

                    Textview(
                        text = "Pickup Today",
                        size = font_14,
                        color = Blackish,
                        fontWeight = FontWeight.Medium
                    )
                }

                /*  Button(
                      onClick = { },
                      colors = ButtonDefaults.buttonColors(
                          containerColor = Color(0xFF4CAF50)
                      ),
                      shape = RoundedCornerShape(50)
                  ) {
                      Textview("View")
                  }*/
            }

            Spacer(Modifier.height(dimen_18))

//            TruckProgressTracker()
            TruckProgressTracker(currentStep = currentStep)

            Spacer(Modifier.height(dimen_18))


            Card(
                colors = CardDefaults.cardColors(
                    containerColor = Whitish
                ),
                shape = RoundedCornerShape(dimen_16)
            ) {

                Column(
                    modifier = Modifier.padding(dimen_16)
                ) {

                    Textview(
                        "Confirming your request...",
                        size = font_16,
                        color = Blackish,
                        fontWeight = FontWeight.Bold
                    )
                    Spacer(Modifier.height(dimen_12))

                    Textview(
                        text = "Please wait while we confirm your pickup request.This usually takes 15–30 minutes.",
                        size = font_12,
                        color = Grey,
                    )
                }
            }

        }
    }
}

@Composable
fun TruckProgressTracker(currentStep: Int) {
    val statuses = listOf(
        "Submitted" to "02 Jul, 02:56 PM",
        "Confirmed" to "Waiting confirmation",
        "Assigned" to "Pending",
        "Completed" to "Pending"
    )

    val animatedProgress = remember { Animatable(0f) }
    LaunchedEffect(currentStep) {
        animatedProgress.animateTo(
            targetValue = currentStep.toFloat(),
            animationSpec = tween(durationMillis = 2000, easing = LinearOutSlowInEasing)
        )
    }

    Box(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = dimen_16)
    ) {
        // Draw the background lines
        Canvas(modifier = Modifier
            .fillMaxWidth()
            .height(dimen_42)
            .padding(horizontal = dimen_32)) {
            
            val stepWidth = size.width / (statuses.size - 1)
            
            // Draw lines between steps
            for (i in 0 until statuses.size - 1) {
                val startX = i * stepWidth
                val endX = (i + 1) * stepWidth
                val y = size.height / 2
                
                // Draw inactive dashed line
                drawLine(
                    color = Color.LightGray,
                    start = Offset(startX, y),
                    end = Offset(endX, y),
                    strokeWidth = 4.dp.toPx(),
                    pathEffect = PathEffect.dashPathEffect(floatArrayOf(10f, 10f), 0f)
                )
                
                // Draw active solid line if progress has reached here
                if (animatedProgress.value > i) {
                    val progressInSection = (animatedProgress.value - i).coerceIn(0f, 1f)
                    drawLine(
                        color = Color(0xFF4CAF50), // Green
                        start = Offset(startX, y),
                        end = Offset(startX + (stepWidth * progressInSection), y),
                        strokeWidth = 4.dp.toPx()
                    )
                }
            }
        }

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.Top
        ) {
            statuses.forEachIndexed { index, (title, subtitle) ->
                val isActive = currentStep >= index
                val isCurrent = currentStep == index
                
                Column(
                    horizontalAlignment = Alignment.CenterHorizontally,
                    modifier = Modifier.weight(1f)
                ) {
                    Box(
                        modifier = Modifier.size(dimen_42),
                        contentAlignment = Alignment.Center
                    ) {
                        // Background circle for icon
                        if (index == 0) {
                            Box(
                                modifier = Modifier
                                    .size(dimen_32)
                                    .background(Color.White, CircleShape)
                                    .border(2.dp, Color(0xFF4CAF50), CircleShape),
                                contentAlignment = Alignment.Center
                            ) {
                                Icon(
                                    imageVector = Icons.Default.Check,
                                    contentDescription = null,
                                    tint = Color(0xFF4CAF50),
                                    modifier = Modifier.size(dimen_18)
                                )
                            }
                        } else if (index == statuses.size - 1) {
                            Box(
                                modifier = Modifier
                                    .size(dimen_32)
                                    .background(Color(0xFFF5F5F5), CircleShape),
                                contentAlignment = Alignment.Center
                            ) {
                                Icon(
                                    imageVector = Icons.Default.Flag,
                                    contentDescription = null,
                                    tint = Color.Gray,
                                    modifier = Modifier.size(dimen_18)
                                )
                            }
                        } else if (index == 2) {
                            Box(
                                modifier = Modifier
                                    .size(dimen_32)
                                    .background(Color(0xFFF5F5F5), CircleShape),
                                contentAlignment = Alignment.Center
                            ) {
                                Icon(
                                    imageVector = Icons.Default.Person,
                                    contentDescription = null,
                                    tint = Color.Gray,
                                    modifier = Modifier.size(dimen_18)
                                )
                            }
                        } else {
                            // Confirmed state slot (can be empty as truck covers it)
                            Box(
                                modifier = Modifier
                                    .size(dimen_32)
                                    .background(Color(0xFFF5F5F5), CircleShape)
                            )
                        }
                    }

                    Spacer(modifier = Modifier.height(dimen_8))

                    Textview(
                        text = title,
                        size = font_12,
                        color = when {
                            index == 0 -> Color(0xFF4CAF50) // Green
                            index == 1 -> Color(0xFFFFC107) // Yellow
                            else -> Color.Gray
                        },
                        fontWeight = FontWeight.Bold
                    )
                    
                    Spacer(modifier = Modifier.height(dimen_4))
                    
                    Textview(
                        text = subtitle,
                        size = font_10,
                        color = Color.Gray,
                        textAlign = TextAlign.Center
                    )
                }
            }
        }
        
        // Draw the animating truck
        BoxWithConstraints(
            modifier = Modifier.fillMaxWidth().padding(top = dimen_4)
        ) {
            val stepWidth = maxWidth / statuses.size
            val truckOffset = (stepWidth * animatedProgress.value) + (stepWidth / 2) - 16.dp // Center truck on step
            
            Image(
                painter = painterResource(R.drawable.green_truck_ic),
                contentDescription = "Truck",
                modifier = Modifier
                    .offset(x = truckOffset)
                    .size(32.dp)
            )
        }
    }
}