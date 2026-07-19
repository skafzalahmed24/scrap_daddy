package com.scrapdaddy.ui.orderdetail

import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Check
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Star
import androidx.compose.material.icons.outlined.Image
import androidx.compose.foundation.horizontalScroll
import androidx.compose.material.icons.outlined.Call
import androidx.compose.material.icons.outlined.Chat
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.CenterAlignedTopAppBar
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.Scaffold
import androidx.compose.material3.TopAppBarDefaults
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.Color.Companion.Black
import androidx.compose.ui.graphics.Color.Companion.DarkGray
import androidx.compose.ui.graphics.Color.Companion.Transparent
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.scrapdaddy.R
import com.scrapdaddy.ui.components.HeaderWithBackIcon
import com.scrapdaddy.ui.components.Textview
import com.scrapdaddy.ui.theme.Blue
import com.scrapdaddy.ui.theme.Green
import com.scrapdaddy.ui.theme.Grey
import com.scrapdaddy.ui.theme.LightGreenish
import com.scrapdaddy.ui.theme.Red
import com.scrapdaddy.ui.theme.SkyBlue
import com.scrapdaddy.ui.theme.White
import com.scrapdaddy.ui.theme.dimen_0
import com.scrapdaddy.ui.theme.dimen_1
import com.scrapdaddy.ui.theme.dimen_12
import com.scrapdaddy.ui.theme.dimen_14
import com.scrapdaddy.ui.theme.dimen_16
import com.scrapdaddy.ui.theme.dimen_2
import com.scrapdaddy.ui.theme.dimen_20
import com.scrapdaddy.ui.theme.dimen_24
import com.scrapdaddy.ui.theme.dimen_4
import com.scrapdaddy.ui.theme.dimen_40
import com.scrapdaddy.ui.theme.dimen_70
import com.scrapdaddy.ui.theme.font_12
import com.scrapdaddy.ui.theme.font_14
import com.scrapdaddy.ui.theme.font_16
import com.scrapdaddy.ui.theme.font_18

@OptIn(ExperimentalMaterial3Api::class)
@Preview(showBackground = true)
@Composable
fun MyOrdersDetailScreen() {

    val timelineSteps = listOf(
        TimelineStep(
            "Submitted",
            "Your pickup request has been received.",
            "Today • 10:00 AM",
            TimelineState.COMPLETED
        ),
        TimelineStep(
            "Confirmed",
            "We have verified and confirmed the request.",
            "Today • 10:15 AM",
            TimelineState.COMPLETED
        ),
        TimelineStep(
            "Assigned",
            "Rahul has accepted your pickup request.",
            "Today • 2:15 PM",
            TimelineState.CURRENT
        ),
        TimelineStep(
            "Completed",
            "Driver is heading to your location.",
            "",
            TimelineState.UPCOMING
        ),
    )

    Box(
        modifier = Modifier.background(brush = Brush.linearGradient(listOf(LightGreenish, White)))
    ) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(dimen_24)
        ) {

            HeaderWithBackIcon(title = "Order Details") {

            }
            Spacer(modifier = Modifier.height(dimen_20))
            
            TopSubcategoryCard()
            Spacer(modifier = Modifier.height(dimen_16))

            PickupDetailsCard()
            Spacer(modifier = Modifier.height(dimen_16))

            ScrapDetailsCard()
            Spacer(modifier = Modifier.height(dimen_16))

            UploadedImagesCard()
            Spacer(modifier = Modifier.height(dimen_24))

            Textview(
                text = "Pickup Progress",
                size = font_18,
                color = Black,
                fontWeight = FontWeight.Bold,
            )
            Spacer(modifier = Modifier.height(dimen_16))

            Column(modifier = Modifier.fillMaxWidth()) {
                timelineSteps.forEachIndexed { index, step ->
                    TimelineItem(
                        step = step,
                        isLast = index == timelineSteps.size - 1
                    )
                }
            }
        }
    }
}


enum class TimelineState {
    COMPLETED, CURRENT, UPCOMING
}

data class TimelineStep(
    val title: String,
    val description: String,
    val dateTime: String,
    val state: TimelineState
)


@Composable
fun TopSubcategoryCard() {
    Card(
        shape = RoundedCornerShape(dimen_16),
        colors = CardDefaults.cardColors(containerColor = White),
        elevation = CardDefaults.cardElevation(defaultElevation = dimen_2),
        modifier = Modifier.fillMaxWidth()
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .background(brush = Brush.horizontalGradient(listOf(LightGreenish, White)))
                .padding(dimen_16),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier
                    .size(dimen_70)
                    .clip(CircleShape)
                    .background(White)
                    .border(dimen_1, Green, CircleShape),
                contentAlignment = Alignment.Center
            ) {
                // Placeholder for Subcategory Image
                Icon(
                    painter = painterResource(R.drawable.green_truck_ic), 
                    contentDescription = null, 
                    tint = Green, 
                    modifier = Modifier.size(dimen_40)
                )
            }
            Spacer(modifier = Modifier.width(dimen_16))
            Column {
                Textview("Washing Machines", size = font_18, fontWeight = FontWeight.Bold, color = Black)
                Spacer(modifier = Modifier.height(dimen_4))
                Textview("Order #PK10245", size = font_14, color = Grey)
            }
        }
    }
}

@Composable
fun PickupDetailsCard() {
    Card(
        shape = RoundedCornerShape(dimen_16),
        colors = CardDefaults.cardColors(containerColor = White),
        elevation = CardDefaults.cardElevation(defaultElevation = dimen_1),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(
            modifier = Modifier.padding(dimen_16),
            verticalArrangement = Arrangement.spacedBy(dimen_12)
        ) {
            Textview(
                text = "Pickup Details",
                size = font_16,
                fontWeight = FontWeight.Bold,
                color = Black
            )
            HorizontalDivider(color = Grey, thickness = dimen_1)
            DetailRowPair("Date", "Thursday, 02 Jul 2026")
            DetailRowPair("Time Slot", "09:00 AM - 12:00 PM")
            
            Column {
                 Textview("Location", size = font_12, color = Grey)
                 Spacer(modifier = Modifier.height(dimen_4))
                 Textview(
                     "Revenue Colony, Kannayya Kapu Nagar, Kakinada, Kakinada Urban, Kakinada, Andhra Pradesh, 533001, India", 
                     size = font_14, 
                     color = Black, 
                     fontWeight = FontWeight.Medium
                 )
            }
        }
    }
}

@Composable
fun ScrapDetailsCard() {
    Card(
        shape = RoundedCornerShape(dimen_16),
        colors = CardDefaults.cardColors(containerColor = White),
        elevation = CardDefaults.cardElevation(defaultElevation = dimen_1),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(
            modifier = Modifier.padding(dimen_16),
            verticalArrangement = Arrangement.spacedBy(dimen_12)
        ) {
            Textview(
                text = "Scrap Details",
                size = font_16,
                fontWeight = FontWeight.Bold,
                color = Black
            )
            HorizontalDivider(color = Grey, thickness = dimen_1)
            DetailRowPair("Category", "N/A")
            DetailRowPair("Subcategory", "Washing Machines")
        }
    }
}

@Composable
fun UploadedImagesCard() {
    Card(
        shape = RoundedCornerShape(dimen_16),
        colors = CardDefaults.cardColors(containerColor = White),
        elevation = CardDefaults.cardElevation(defaultElevation = dimen_1),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(
            modifier = Modifier.padding(dimen_16),
            verticalArrangement = Arrangement.spacedBy(dimen_12)
        ) {
            Textview(
                text = "Uploaded Images",
                size = font_16,
                fontWeight = FontWeight.Bold,
                color = Black
            )
            HorizontalDivider(color = Grey, thickness = dimen_1)
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .horizontalScroll(rememberScrollState()),
                horizontalArrangement = Arrangement.spacedBy(dimen_12)
            ) {
                // sample images
                repeat(4) {
                    Box(
                        modifier = Modifier
                            .size(dimen_70)
                            .clip(RoundedCornerShape(dimen_8))
                            .background(Color(0xFFF0F0F0))
                            .border(dimen_1, Color(0xFFE0E0E0), RoundedCornerShape(dimen_8)),
                        contentAlignment = Alignment.Center
                    ) {
                        Icon(
                            imageVector = Icons.Outlined.Image, 
                            contentDescription = "Sample Image", 
                            tint = Color.Gray,
                            modifier = Modifier.size(dimen_24)
                        )
                    }
                }
            }
        }
    }
}

@Composable
fun DetailRowPair(label: String, value: String) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Textview(label, size = font_12, color = Grey)
        Textview(value, size = font_14, color = Black, fontWeight = FontWeight.Medium)
    }
}


@Composable
fun TimelineItem(step: TimelineStep, isLast: Boolean) {
    Row(modifier = Modifier.fillMaxWidth()) {
        // Left Column (Icon + Line)
        Column(
            horizontalAlignment = Alignment.CenterHorizontally,
            modifier = Modifier.width(dimen_40)
        ) {
            // Icon Indicator
            Box(
                modifier = Modifier
                    .size(dimen_24)
                    .clip(CircleShape)
                    .background(
                        when (step.state) {
                            TimelineState.COMPLETED -> Green
                            TimelineState.CURRENT -> Blue
                            TimelineState.UPCOMING -> White
                        }
                    )
                    .border(
                        width = if (step.state == TimelineState.UPCOMING) dimen_1 else dimen_0,
                        color = if (step.state == TimelineState.UPCOMING) Grey else Transparent,
                        shape = CircleShape
                    ),
                contentAlignment = Alignment.Center
            ) {
                when (step.state) {
                    TimelineState.COMPLETED -> {
                        Icon(
                            Icons.Default.Check,
                            contentDescription = "Completed",
                            tint = White,
                            modifier = Modifier.size(dimen_16)
                        )
                    }

                    TimelineState.CURRENT -> {
                        // Replace R.drawable.ic_launcher_foreground with your actual truck image resource (e.g. R.drawable.truck_image)
                        Image(
                            painter = painterResource(id = R.drawable.track_truck_ic),
                            contentDescription = "Current Stage",
                            modifier = Modifier.size(dimen_16)
                        )
                    }

                    TimelineState.UPCOMING -> {}
                }
            }

            // Connecting Line
            if (!isLast) {
                Box(
                    modifier = Modifier
                        .width(dimen_2)
                        .height(dimen_70) // Adjust based on desired spacing
                        .background(
                            if (step.state == TimelineState.COMPLETED) Green else Grey
                        )
                )
            }
        }

        Spacer(modifier = Modifier.width(dimen_12))

        // Right Column (Texts)
        Column(
            modifier = Modifier
                .weight(1f)
                .padding(bottom = if (isLast) dimen_0 else dimen_24)
        ) {
            Textview(
                text = step.title,
                size = font_16,
                color = if (step.state == TimelineState.UPCOMING) Grey else Black,
                fontWeight = if (step.state == TimelineState.CURRENT) FontWeight.Bold else FontWeight.SemiBold,
            )

            if (step.description.isNotEmpty()) {
                Spacer(modifier = Modifier.height(dimen_4))
                Textview(
                    text = step.description,
                    size = font_14,
                    color = Grey
                )
            }

            if (step.dateTime.isNotEmpty()) {
                Spacer(modifier = Modifier.height(dimen_4))
                Textview(
                    text = step.dateTime,
                    size = font_12,
                    color = DarkGray,
                    fontWeight = FontWeight.Medium,
                )
            }
        }
    }
}
