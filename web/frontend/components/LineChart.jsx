import React, {useState} from 'react';
import ReactECharts from 'echarts-for-react';

const LineChart = ({ title,color ,data }) => {
    // Sample data for the multi-line chart

    // Extracting the months, value1, and value2 from the data
    // const months = data.map(item => item.month);
    const dates   = data && data.length > 0 ? data.map(item => item.date) : [];
    const value   = data && data.length > 0 ? data.map(item => item.value) : [];

    const graphData = {

        name: title,
        type: 'line',
        data: value, // Assuming 'value' is the data you want to use
        smooth: false,
        lineStyle: {
            color: color, // Use the color from orderStatus
            width: 2,
            type: 'solid',
        },
        itemStyle: {
            color: color, // Use the color from orderStatus
        },
        // You can uncomment the areaStyle properties if needed
    };
    // return graphData.name;

    const legend = title;
    const option = {
        title: {
            text: '',
            left: 'center',
        },
        legend: {
            data: legend,
            top: 'bottom',
            right: '10%',

            textStyle: {
                color: '#666', // Change the text color of the legend
                fontSize: 12, // Change the font size of the legend text
            },
            itemWidth: 20,
            itemHeight: 10,
            itemGap: 5,
            // Set the icon shape to "rect" to display the legend as a box
            icon: 'rect',
        },
        grid: {
            bottom: '15%', // Add some space at the bottom for the legend
        },
        tooltip: {
            trigger: 'axis',
        },
        xAxis: {
            type: 'category',
            data: dates, // Use the dates array for the xAxis data
            /*axisLabel: {
                formatter: (value) => {
                    return value.slice(5, 10); // Display only the month-day part on the xAxis labels
                },
            },*/
        },
        yAxis: {
            type: 'value',
            min: 0, // Set the minimum value for the y-axis to 0
        },
        series: graphData,
    };

    return <ReactECharts option={option} style={{ height: '400px' }} />;
};

export default LineChart;
