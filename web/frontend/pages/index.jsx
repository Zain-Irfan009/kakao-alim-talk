import React, { useCallback, useEffect, useRef, useState,useContext } from "react";
import {
  Page,
  Button,
  BlockStack,
  Popover,
  ActionList,
  Icon,
  OptionList,
  Card,
  Text,
  InlineStack,
    Loading,
  EmptySearchResult,
  IndexTable,
  LegacyStack,
  Badge,
  Box,
  RangeSlider,
  IndexFilters,
  useSetIndexFiltersMode,
  IndexFiltersMode,
  ChoiceList,
  TextField,
  useIndexResourceState,
  Pagination,
  Grid,
  InlineGrid,
  Select,
  Scrollable,
  DatePicker,
  useBreakpoints,
  ButtonGroup,
  Layout,
} from "@shopify/polaris";
import { CalendarIcon, MinusCircleIcon, CheckSmallIcon, ArrowRightIcon, EmailIcon } from "@shopify/polaris-icons";
import LineChart from "../components/LineChart";
import { useNavigate } from "react-router-dom";
import noMessageImg from "../assets/no-message.png";
import {getSessionToken} from "@shopify/app-bridge-utils";
import SkeletonTable from "../components/SkeletonTable.jsx"
import axios from "axios";
import {AppContext, ProductsCard} from "../components";
import {useAppBridge} from "@shopify/app-bridge-react";

export default function Dashboard() {
  const navigate = useNavigate();
    const appBridge = useAppBridge();
    const { apiUrl } = useContext(AppContext);
  const [popoverActive, setPopoverActive] = useState(false);
    const [newDateRange, setNewDateRange] = useState('');
  const [popoverActiveButtonLoading, setPopoverActiveButtonLoading] = useState(false);
  const [displayDataAction, setDisplayDataAction] = useState("Today");
    const [loading, setLoading] = useState(true)
    const [newOrder, setNewOrder] = useState(0);
    const [orderUpdate, setOrderUpdate] = useState(0);
    const [orderCancel, setOrderCancel] = useState(0);
    const [orderFulfill, setOrderFulfill] = useState(0);

    const [totalMessages, setTotalMessages] = useState(0);
    const [toggleData, setToggleData] = useState(false);
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [latestMessages, setLatestMessages] = useState([]);

    const { selectedResources, allResourcesSelected, handleSelectionChange } =
        useIndexResourceState(latestMessages);
  const togglePopoverActive = useCallback(() => setPopoverActive((popoverActive) => !popoverActive), []);

  const handleDisplayDataAction = useCallback((content) => {
    setPopoverActiveButtonLoading(true);
    setPopoverActive((popoverActive) => !popoverActive);
    switch (content) {
      case "today":
        setDisplayDataAction("Today");
        setTimeout(() => {
          setPopoverActiveButtonLoading(false);
        }, 1000);
        break;
      case "last_7_days":
        setDisplayDataAction("Last 7 days");
        setTimeout(() => {
          setPopoverActiveButtonLoading(false);
        }, 1000);
        break;
      case "last_28_days":
        setDisplayDataAction("Last 28 days");
        setTimeout(() => {
          setPopoverActiveButtonLoading(false);
        }, 1000);
        break;
      default:
        break;
    }
  }, []);
  const activator = (
    <Button loading={popoverActiveButtonLoading} onClick={togglePopoverActive} icon={<Icon source={CalendarIcon} />}>
      {displayDataAction}
    </Button>
  );

  const orders = [];



  const handleActiveDateRange= (activeDateRange)=>{

      const { period } = activeDateRange;
      const start_date = new Date(period.since).toLocaleDateString('en-CA');  // 'en-CA' for YYYY-MM-DD format
      const end_date = new Date(period.until).toLocaleDateString('en-CA');
      setStartDate(start_date)
      setEndDate(end_date)
  };





  const resourceName = {
    singular: "customer",
    plural: "customers",
  };

    const formatDateTable = (created_at) => {
        const months = [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        const date = new Date(created_at);
        const monthName = months[date.getMonth()];
        const day = date.getDate();
        const year = date.getFullYear();

        const formattedDate = `${monthName} ${day}, ${year}`;
        return formattedDate;
    }

  const emptyStateMarkup = (
    <Box padding={"600"}>
      <BlockStack inlineAlign="center">
        <Box maxWidth="100%">
          <BlockStack inlineAlign="center">
            <BlockStack gap={300}>
              <div className="flex justify-center items-center">
                <img src={noMessageImg} width={48} height={48} alt="" />
              </div>
              <Text as="p" variant="headingLg" alignment="center">
                No message has been found
              </Text>
              <Text as="p" variant="bodyMd" tone="subdued">
                No messages are available. Consider creating a new one to get started!
              </Text>
            </BlockStack>
          </BlockStack>
        </Box>
      </BlockStack>
    </Box>
  );

  const rowMarkup = latestMessages.map(({ id, sms,sms_type, created_at }, index) => (
      <IndexTable.Row id={id} key={id} position={index}>
      {/*<IndexTable.Cell>*/}
      {/*  <Text variant="bodyMd" fontWeight="bold" as="span">*/}
      {/*    {id}*/}
      {/*  </Text>*/}
      {/*</IndexTable.Cell>*/}

          <IndexTable.Cell>
              {sms_type === 'new_order' ? (
                  <>
                      <Badge tone="success">Order Create</Badge>

                  </>
              ) : sms_type === 'order_update' ? (
                  <>

                      <Badge tone="info">Order Update</Badge>

                  </>
              ) : sms_type === 'order_fulfilled' ? (
                  <>

                      <Badge tone="warning">Order Fulfilled</Badge>

                  </>
              ) : sms_type === 'order_cancel' ? (
                  <>

                      <Badge tone="critical">Order Cancelled</Badge>

                  </>
              ) : null}
          </IndexTable.Cell>

          <IndexTable.Cell>{sms}</IndexTable.Cell>
          <IndexTable.Cell>{created_at != null ? formatDateTable(created_at) : "---"}</IndexTable.Cell>
    </IndexTable.Row>
  ));

  const cardData = [
    {
      id: 1,
      title: "Order created",
      TotalValue: newOrder,
    },
    {
      id: 2,
      title: "Order fulfilled",
      TotalValue: orderFulfill,
    },
    {
      id: 3,
      title: "Order cancelled",
      TotalValue: orderCancel,
      Cutvalue: "3",
      message: "Same as previous 7 days",
    },
    {
      id: 4,
      title: "Order updated",
      TotalValue: orderUpdate,
      Cutvalue: "0",
      message: "Same as previous 7 days",
    },

  ];

  // const chart_data = {
  //   id: 1,
  //   title: "Messages",
  //   color: "#1e93eb",
  //   value: 8,
  //   data: [
  //     { date: "Oct 01, 2023", value: 10 },
  //     { date: "Oct 02, 2023", value: 20 },
  //     { date: "Oct 03, 2023", value: 15 },
  //     { date: "Oct 04, 2023", value: 12 },
  //     { date: "Oct 05, 2023", value: 18 },
  //     { date: "Oct 06, 2023", value: 16 },
  //     { date: "Oct 07, 2023", value: 0 },
  //     { date: "Oct 08, 2023", value: 25 },
  //     { date: "Oct 09, 2023", value: 14 },
  //     { date: "Oct 10, 2023", value: 0 },
  //     { date: "Oct 11, 2023", value: 22 },
  //     { date: "Oct 12, 2023", value: 19 },
  //     { date: "Oct 13, 2023", value: 0 },
  //     { date: "Oct 14, 2023", value: 0 },
  //     { date: "Oct 15, 2023", value: 0 },
  //     { date: "Oct 16, 2023", value: 21 },
  //     { date: "Oct 17, 2023", value: 24 },
  //     { date: "Oct 18, 2023", value: 0 },
  //     { date: "Oct 19, 2023", value: 18 },
  //     { date: "Oct 20, 2023", value: 25 },
  //     { date: "Oct 21, 2023", value: 12 },
  //     { date: "Oct 22, 2023", value: 14 },
  //     { date: "Oct 23, 2023", value: 23 },
  //     { date: "Oct 24, 2023", value: 17 },
  //     { date: "Oct 25, 2023", value: 0 },
  //     { date: "Oct 26, 2023", value: 14 },
  //     { date: "Oct 27, 2023", value: 0 },
  //     { date: "Oct 28, 2023", value: 21 },
  //     { date: "Oct 29, 2023", value: 0 },
  //     { date: "Oct 30, 2023", value: 25 },
  //     { date: "Oct 31, 2023", value: 25 },
  //   ],
  // };


    const [chartData, setChartData] = useState({
        id: 1,
        title: "Messages",
        color: "#1e93eb",
        value: 8,
        data: [], // Start with an empty array
    });
    const { mdDown, lgUp } = useBreakpoints();
    const [btnLoading , setBtnLoading] = useState(false)
    const shouldShowMultiMonth = lgUp;
    const today = new Date(new Date().setHours(0, 0, 0, 0));
    const yesterday = new Date(new Date(new Date().setDate(today.getDate() - 1)).setHours(0, 0, 0, 0));
    const ranges = [
        {
            title: "Today",
            alias: "today",
            period: {
                since: today,
                until: today,
            },
        },
        {
            title: "Last 7 days",
            alias: "last7days",
            period: {
                since: new Date(new Date(new Date().setDate(today.getDate() - 7)).setHours(0, 0, 0, 0)),
                until: yesterday,
            },
        },
        {
            title: "Last 30 days",
            alias: "last30days",
            period: {
                since: new Date(new Date(new Date().setDate(today.getDate() - 30)).setHours(0, 0, 0, 0)),
                until: yesterday,
            },
        },
        {
            title: "Last 60 days",
            alias: "last60days",
            period: {
                since: new Date(new Date(new Date().setDate(today.getDate() - 60)).setHours(0, 0, 0, 0)),
                until: yesterday,
            },
        },
        {
            title: "Last 90 days",
            alias: "last90days",
            period: {
                since: new Date(new Date(new Date().setDate(today.getDate() - 90)).setHours(0, 0, 0, 0)),
                until: yesterday,
            },
        },
        {
            title: "Last 365 days",
            alias: "last365days",
            period: {
                since: new Date(new Date(new Date().setDate(today.getDate() - 365)).setHours(0, 0, 0, 0)),
                until: yesterday,
            },
        },
        {
            title: "Last month",
            alias: "lastmonth",
            period: {
                since: new Date(new Date().getFullYear(), new Date().getMonth() - 1, 1),
                until: new Date(new Date().getFullYear(), new Date().getMonth(), 0),
            },
        },
        {
            title: "Last 12 months",
            alias: "last12months",
            period: {
                since: new Date(new Date().setFullYear(new Date().getFullYear() - 1, new Date().getMonth(), 1)),
                until: yesterday,
            },
        },
        {
            title: "Last year",
            alias: "lastyear",
            period: {
                since: new Date(new Date().setFullYear(new Date().getFullYear() - 1, 0, 1)),
                until: new Date(new Date().setFullYear(new Date().getFullYear() - 1, 11, 31)),
            },
        },
        {
            title: "Week to Date",
            alias: "weektodate",
            period: {
                since: new Date(new Date().setDate(new Date().getDate() - new Date().getDay())),
                until: today,
            },
        },
        {
            title: "Month to Date",
            alias: "monthtodate",
            period: {
                since: new Date(today.getFullYear(), today.getMonth(), 1),
                until: today,
            },
        },
        {
            title: "Quarter to Date",
            alias: "quartertodate",
            period: {
                since: new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 1),
                until: today,
            },
        },
        {
            title: "Year to Date",
            alias: "yeartodate",
            period: {
                since: new Date(today.getFullYear(), 0, 1),
                until: today,
            },
        },
    ];
    const [activeDateRange, setActiveDateRange] = useState(ranges[0]);

    // useEffect(() => {
    //
    // handleActiveDateRange(activeDateRange)
    //
    // }, [activeDateRange]);

    const [inputValues, setInputValues] = useState({});
    const [{ month, year }, setDate] = useState({
        month: activeDateRange.period.since.getMonth(),
        year: activeDateRange.period.since.getFullYear(),
    });
    const datePickerRef = useRef(null);
    const VALID_YYYY_MM_DD_DATE_REGEX = /^\d{4}-\d{1,2}-\d{1,2}/;
    const [popoverActiveOrderDate, setPopoverActiveOrderDate] = useState(false);



    function isDate(date) {
        return !isNaN(new Date(date).getDate());
    }
    function isValidYearMonthDayDateString(date) {
        return VALID_YYYY_MM_DD_DATE_REGEX.test(date) && isDate(date);
    }
    function isValidDate(date) {
        return date.length === 10 && isValidYearMonthDayDateString(date);
    }

    function parseYearMonthDayDateString(input) {
        // Date-only strings (e.g. "1970-01-01") are treated as UTC, not local time
        // when using new Date()
        // We need to split year, month, day to pass into new Date() separately
        // to get a localized Date
        const [year, month, day] = input.split("-");
        return new Date(Number(year), Number(month) - 1, Number(day));
    }
    function formatDateToYearMonthDayDateString(date) {
        const year = String(date.getFullYear());
        let month = String(date.getMonth() + 1);
        let day = String(date.getDate());
        if (month.length < 2) {
            month = String(month).padStart(2, "0");
        }
        if (day.length < 2) {
            day = String(day).padStart(2, "0");
        }
        return [year, month, day].join("-");
    }
    function formatDate(date) {
        return formatDateToYearMonthDayDateString(date);
    }
    function nodeContainsDescendant(rootNode, descendant) {
        if (rootNode === descendant) {
            return true;
        }
        let parent = descendant.parentNode;
        while (parent != null) {
            if (parent === rootNode) {
                return true;
            }
            parent = parent.parentNode;
        }
        return false;
    }
    function isNodeWithinPopover(node) {
        return datePickerRef?.current ? nodeContainsDescendant(datePickerRef.current, node) : false;
    }
    function handleStartInputValueChange(value) {
        setInputValues((prevState) => {
            return { ...prevState, since: value };
        });
        if (isValidDate(value)) {
            const newSince = parseYearMonthDayDateString(value);
            setActiveDateRange((prevState) => {
                const newPeriod =
                    prevState.period && newSince <= prevState.period.until
                        ? { since: newSince, until: prevState.period.until }
                        : { since: newSince, until: newSince };
                return {
                    ...prevState,
                    period: newPeriod,
                };
            });
        }
    }
    function handleEndInputValueChange(value) {
        setInputValues((prevState) => ({ ...prevState, until: value }));
        if (isValidDate(value)) {
            const newUntil = parseYearMonthDayDateString(value);
            setActiveDateRange((prevState) => {
                const newPeriod =
                    prevState.period && newUntil >= prevState.period.since
                        ? { since: prevState.period.since, until: newUntil }
                        : { since: newUntil, until: newUntil };
                return {
                    ...prevState,
                    period: newPeriod,
                };
            });
        }
    }
    function handleInputBlur({ relatedTarget }) {
        const isRelatedTargetWithinPopover = relatedTarget != null && isNodeWithinPopover(relatedTarget);
        // If focus moves from the TextField to the Popover
        // we don't want to close the popover
        if (isRelatedTargetWithinPopover) {
            return;
        }
        setPopoverActiveOrderDate(false);
    }
    function handleMonthChange(month, year) {
        setDate({ month, year });
    }
    function handleCalendarChange({ start, end }) {
        const newDateRange = ranges.find((range) => {
            return range.period.since.valueOf() === start.valueOf() && range.period.until.valueOf() === end.valueOf();
        }) || {
            alias: "custom",
            title: "Custom",
            period: {
                since: start,
                until: end,
            },
        };
        setActiveDateRange(newDateRange);
    }
    function apply() {
        setToggleData(prev => !prev);
        setBtnLoading(true)

    }
    function cancel() {
        setPopoverActiveOrderDate(false);
    }


    const fetchData = async () => {
        if (inputValues.since === undefined) {
            const today = new Date();

            // Format today's date as YYYY-MM-DD
            const formattedToday = today.toISOString().split('T')[0];

            // Set the `since` value to today's date
            inputValues.since = formattedToday;
            inputValues.until = formattedToday;
        }
        try {
            let sessionToken = await getSessionToken(appBridge);

            const response = await axios.get(
                `${apiUrl}dashboard-logs?start_date=${inputValues.since}&end_date=${inputValues.until}`,
                {
                    headers: {
                        Authorization: `Bearer ${sessionToken}`,
                    },
                }
            );

            if (response?.status === 200) {

                setNewOrder(response?.data?.new_order);
                setOrderUpdate(response?.data?.order_update);
                setOrderFulfill(response?.data?.order_fulfilled);
                setOrderCancel(response?.data?.order_cancel);
                setTotalMessages(response?.data?.total_messages);
                setChartData(prevData => ({
                    ...prevData,
                    data: response?.data?.chart_data // Assuming the API sends {chart_data: [{date: ..., value: ...}]}
                }));
                setLatestMessages(response?.data?.latest_messages)

                setPopoverActiveOrderDate(false);
                setBtnLoading(false);
                setLoading(false);
            }
        } catch (error) {
            console.error("Error fetching data:", error);
        } finally {
            setLoading(false);
            setBtnLoading

        }
    };
    useEffect(() => {
            fetchData();
    }, [toggleData]);




    useEffect(() => {
        if (activeDateRange) {
            setInputValues({
                since: formatDate(activeDateRange.period.since),
                until: formatDate(activeDateRange.period.until),
            });
            function monthDiff(referenceDate, newDate) {
                return newDate.month - referenceDate.month + 12 * (referenceDate.year - newDate.year);
            }
            const monthDifference = monthDiff(
                { year, month },
                {
                    year: activeDateRange.period.until.getFullYear(),
                    month: activeDateRange.period.until.getMonth(),
                },
            );
            if (monthDifference > 1 || monthDifference < 0) {
                setDate({
                    month: activeDateRange.period.until.getMonth(),
                    year: activeDateRange.period.until.getFullYear(),
                });
            }
        }
    }, [activeDateRange]);

    const buttonValue =
        activeDateRange.title === "Custom"
            ? activeDateRange.period.since.toDateString() + " - " + activeDateRange.period.until.toDateString()
            : activeDateRange.title;


    return (

    <Page title="Dashboard">
        {loading ?
            <span>
                    <Loading/>
                       <SkeletonTable/>
                </span>
            :
            <Layout>
                <Layout.Section>


                    <div className="px-2 sm:px-0 sm:pb-10">
                        <BlockStack gap="400">
                            <Popover
                                active={popoverActiveOrderDate}
                                autofocusTarget="none"
                                preferredAlignment="left"
                                preferredPosition="below"
                                fluidContent
                                sectioned={false}
                                fullHeight
                                activator={
                                    <Button size="slim" icon={CalendarIcon} onClick={() => setPopoverActiveOrderDate(!popoverActiveOrderDate)}>
                                        {buttonValue}
                                    </Button>
                                }
                                onClose={() => setPopoverActiveOrderDate(false)}
                            >
                                <Popover.Pane fixed>
                                    <InlineGrid
                                        columns={{
                                            xs: "1fr",
                                            mdDown: "1fr",
                                            md: "max-content max-content",
                                        }}
                                        gap={0}
                                        ref={datePickerRef}
                                    >
                                        <Box
                                            maxWidth={mdDown ? "516px" : "212px"}
                                            width={mdDown ? "100%" : "212px"}
                                            padding={{ xs: 500, md: 0 }}
                                            paddingBlockEnd={{ xs: 100, md: 0 }}
                                        >
                                            {mdDown ? (
                                                <Select
                                                    label="dateRangeLabel"
                                                    labelHidden
                                                    onChange={(value) => {
                                                        const result = ranges.find(({ title, alias }) => title === value || alias === value);
                                                        setActiveDateRange(result);
                                                    }}
                                                    value={activeDateRange?.title || activeDateRange?.alias || ""}
                                                    options={ranges.map(({ alias, title }) => title || alias)}
                                                />
                                            ) : (
                                                <Scrollable style={{ height: "334px" }}>
                                                    <OptionList
                                                        options={ranges.map((range) => ({
                                                            value: range.alias,
                                                            label: range.title,
                                                        }))}
                                                        selected={activeDateRange.alias}
                                                        onChange={(value) => {
                                                            setActiveDateRange(ranges.find((range) => range.alias === value[0]));
                                                        }}
                                                    />
                                                </Scrollable>
                                            )}
                                        </Box>
                                        <Box padding={{ xs: 500 }} maxWidth={mdDown ? "320px" : "516px"}>
                                            <BlockStack gap="400">
                                                <InlineStack gap="200" wrap={false}>
                                                    <div style={{ flexGrow: 1 }}>
                                                        <TextField
                                                            role="combobox"
                                                            label={"Since"}
                                                            labelHidden
                                                            prefix={<Icon source={CalendarIcon} />}
                                                            value={inputValues.since}
                                                            onChange={handleStartInputValueChange}
                                                            onBlur={handleInputBlur}
                                                            placeholder="YYYY-MM-DD"
                                                            autoComplete="off"
                                                        />
                                                    </div>
                                                    <Icon source={ArrowRightIcon} />
                                                    <div style={{ flexGrow: 1 }}>
                                                        <TextField
                                                            role="combobox"
                                                            label={"Until"}
                                                            labelHidden
                                                            prefix={<Icon source={CalendarIcon} />}
                                                            value={inputValues.until}
                                                            onChange={handleEndInputValueChange}
                                                            onBlur={handleInputBlur}
                                                            placeholder="YYYY-MM-DD"
                                                            autoComplete="off"
                                                        />
                                                    </div>
                                                </InlineStack>
                                                <div>
                                                    <DatePicker
                                                        month={month}
                                                        year={year}
                                                        selected={{
                                                            start: activeDateRange.period.since,
                                                            end: activeDateRange.period.until,
                                                        }}
                                                        onMonthChange={handleMonthChange}
                                                        onChange={handleCalendarChange}
                                                        multiMonth={shouldShowMultiMonth}
                                                        allowRange
                                                    />
                                                </div>
                                            </BlockStack>
                                        </Box>
                                    </InlineGrid>
                                </Popover.Pane>
                                <Popover.Pane fixed>
                                    <Popover.Section>
                                        <InlineStack align="end">
                                            <ButtonGroup>
                                                <Button onClick={cancel}>Cancel</Button>
                                                <Button variant="primary" onClick={apply} loading={btnLoading}>
                                                    Apply
                                                </Button>
                                            </ButtonGroup>
                                        </InlineStack>
                                    </Popover.Section>
                                </Popover.Pane>
                            </Popover>
                            <div class="grid grid-cols-2 sm:grid-cols-2 gap-2">
                                {cardData?.map((data) => (
                                    <Card key={data?.id}>
                                        <BlockStack gap="400">
                                            <InlineStack>
                                                <div class="flex flex-grow">
                                                    <Text variant="headingMd" fontWeight="semibold" as="h2">
                                                        {data?.title}
                                                    </Text>
                                                </div>
                                                <Icon source={MinusCircleIcon} tone="subdued"/>
                                            </InlineStack>
                                            <div class="flex items-center h-7 space-x-1">
                                                <Text variant="headingLg" as="span" tone="subdued">
                                                    {data?.TotalValue}
                                                </Text>
                                            </div>
                                        </BlockStack>
                                    </Card>
                                ))}
                            </div>
                            <Card>
                                <BlockStack gap="800">
                                    <div class="opacity-100">
                                        <BlockStack gap="200">
                                            <Text variant="headingMd" as="h2" fontWeight="semibold">
                                                Sent Messages
                                            </Text>
                                            <div class="flex items-center h-7">
                                                <Text variant="headingLg" as="span" tone="critical">
                                                    {totalMessages}
                                                </Text>
                                            </div>
                                            <Text as="span" tone="subdued">
                                                {totalMessages} messages sent {activeDateRange.title}
                                            </Text>
                                        </BlockStack>
                                    </div>
                                    <div class="w-full">
                                        {/*<LineChart title={chart_data?.title} color={chart_data?.color}*/}
                                        {/*           data={chart_data?.data}/>*/}
                                        <LineChart title={chartData?.title} color={chartData?.color}
                                                   data={chartData?.data}/>
                                    </div>
                                </BlockStack>
                            </Card>
                            <Text variant="headingMd" fontWeight="semibold" as="h2">
                                Latest messages
                            </Text>
                            <Card>
                                <IndexTable
                                    resourceName={resourceName}
                                    itemCount={latestMessages.length}
                                    emptyState={emptyStateMarkup}
                                    headings={[
                                        {title: "Message Type"},
                                        {title: "Message"},
                                        {title: "Date"},

                                    ]}
                                    selectable={false}
                                >

                                    {rowMarkup}
                                </IndexTable>
                                {/* <div class="border-t p-5">
                  <LegacyStack alignment="center" distribution="center">
                    <Button>View all</Button>
                  </LegacyStack>
                </div> */}
                            </Card>
                        </BlockStack>
                    </div>
                </Layout.Section>
            </Layout>
        }
    </Page>
  );
}

